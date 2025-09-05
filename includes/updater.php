<?php
if (!defined('ABSPATH')) exit;
/* Ensure this file is required from spike.php */

add_action('init', function(){
  // Try common locations for the PUC library
  $candidates=[
    __DIR__.'/plugin-update-checker/plugin-update-checker.php',
    __DIR__.'/../vendor/plugin-update-checker/plugin-update-checker.php',
    __DIR__.'/../vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php',
  ];
  foreach($candidates as $p){ if(file_exists($p)){ require_once $p; break; } }
  if (!class_exists('Puc_v4_Factory')) return;

  // Point to your GitHub repo
  $checker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/emkowale/spike/',
    dirname(__DIR__).'/spike.php',
    'spike'
  );
  $checker->setBranch('main'); // default branch
  // If the repo is private, uncomment and add a token:
  // $checker->setAuthentication('YOUR_GITHUB_TOKEN');
});
