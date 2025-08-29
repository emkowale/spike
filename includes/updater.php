<?php
if (!defined('ABSPATH')) exit;

class Spike_Git_Updater {
  const OWNER='emkowale', REPO='spike', SLUG='spike/spike.php', TTL=21600, TKEY='spike_update_data';

  static function boot(){
    add_filter('pre_set_site_transient_update_plugins',[__CLASS__,'check']);
    add_filter('plugins_api',[__CLASS__,'info'],10,3);
    add_filter('upgrader_source_selection',[__CLASS__,'rename'],10,4);
  }

  protected static function latest(){
    if ($c=get_transient(self::TKEY)) return $c;
    $r=wp_remote_get("https://api.github.com/repos/".self::OWNER."/".self::REPO."/releases/latest",
      ['headers'=>['User-Agent'=>'WP-Spike']]);
    if (is_wp_error($r)) return false;
    $j=json_decode(wp_remote_retrieve_body($r),true);
    if (empty($j['tag_name'])) return false;
    $zip = !empty($j['assets'][0]['browser_download_url']) ? $j['assets'][0]['browser_download_url'] : ($j['zipball_url'] ?? '');
    $c=['version'=>ltrim($j['tag_name'],'v'),'zip'=>$zip,'name'=>$j['name']??$j['tag_name'],'body'=>$j['body']??''];
    set_transient(self::TKEY,$c,self::TTL);
    return $c;
  }

  static function check($trans){
    if (empty($trans->checked[self::SLUG])) return $trans;
    if (!$l=self::latest()) return $trans;
    $cur=$trans->checked[self::SLUG];
    if (version_compare($cur,$l['version'],'>=')) return $trans;
    $obj=(object)['slug'=>'spike','plugin'=>self::SLUG,'new_version'=>$l['version'],
      'url'=>"https://github.com/".self::OWNER."/".self::REPO,'package'=>$l['zip']];
    $trans->response[self::SLUG]=$obj; return $trans;
  }

  static function info($res,$action,$args){
    if ($action!=='plugin_information' || ($args->slug??'')!=='spike') return $res;
    if (!$l=self::latest()) return $res;
    return (object)['name'=>'Spike','slug'=>'spike','version'=>$l['version'],
      'sections'=>['description'=>$l['body'] ?: 'Spike updates via GitHub releases.'],
      'homepage'=>"https://github.com/".self::OWNER."/".self::REPO];
  }

  static function rename($src,$remote_source,$upgrader,$extra){
    $base=basename($src); if ($base==='spike') return $src;
    $dest=$upgrader->skin->result['destination'].'/spike';
    return @rename($src,$dest) ? $dest : $src;
  }
}
Spike_Git_Updater::boot();
