<?php
if (!defined('ABSPATH')) exit;

/**
 * GitHub updates for Spike via Plugin Update Checker (PUC).
 * Prefers GitHub Release Assets so your attached ZIP (with vendor/) is used.
 */
add_action('init', function () {
  // Load PUC from common locations (bundled or composer)
  foreach ([
    __DIR__.'/plugin-update-checker/plugin-update-checker.php',
    __DIR__.'/../vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php',
    __DIR__.'/../vendor/plugin-update-checker/plugin-update-checker.php',
  ] as $p) { if (file_exists($p)) { require_once $p; break; } }

  $factory = class_exists('Puc_v5_Factory') ? 'Puc_v5_Factory' : (class_exists('Puc_v4_Factory') ? 'Puc_v4_Factory' : '');
  if (!$factory) return;

  $checker = $factory::buildUpdateChecker(
    'https://github.com/emkowale/spike/',   // GitHub repo
    dirname(__DIR__).'/spike.php',          // Main plugin file
    'spike'                                 // Plugin slug
  );

  // Track main branch
  if (method_exists($checker, 'setBranch')) $checker->setBranch('main');

  // Prefer Release Assets (uses the ZIP you attach to the Release)
  if (method_exists($checker, 'getVcsApi')) {
    $api = $checker->getVcsApi();
    if ($api && method_exists($api, 'enableReleaseAssets')) {
      $api->enableReleaseAssets();
      // Optional: filter which asset to use by name:
      // $api->setAssetFilter(function($assets){ return array_values(array_filter($assets, fn($a)=>preg_match('/^spike-.*\.zip$/i',$a->name))); });
    }
  }

  // Private repo? uncomment and add a PAT with 'repo' scope:
  // $checker->setAuthentication('YOUR_GITHUB_TOKEN');
});
