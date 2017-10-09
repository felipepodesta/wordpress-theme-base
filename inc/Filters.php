<?php
namespace Vincit\Filters;

function title_prefix($title) {
  $dev = "D";
  $production = "P";
  if (\Vincit\WP\is_prod() && is_user_logged_in()) {
    return "[$production] $title";
  } else if (\Vincit\WP\is_dev()) {
    return "[$dev] $title";
  }

  // If both fail, fallback into this.
  $domains = [
    ".dev" => $dev,
    ".local" => $dev,
    "localhost" => $dev,
    ".seravo" => $production,
    ".wp-palvelu" => $production,
    get_site_url() => $production,
  ];

  foreach ($domains as $domain => $tag) {
    if (strpos(\Vincit\WP\current_url(), $domain) > -1) {
      if ($tag === $production) {
        if (!is_user_logged_in()) {
          return $title;
        }
      }
      return "[$tag] $title";
    }
  }
  return $title;
}

add_filter("the_seo_framework_pro_add_title", "\\Vincit\\Filters\\title_prefix");
add_filter("admin_title", "\\Vincit\\Filters\\title_prefix");
add_filter("wp_title", "\\Vincit\\Filters\\title_prefix");
