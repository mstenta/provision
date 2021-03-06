
<?php if ($this->ssl_enabled && $this->ssl_key) : ?>

  <VirtualHost <?php print "{$ip_address}:{$http_ssl_port}"; ?>>
  <?php if ($this->site_mail) : ?>
    ServerAdmin <?php  print $this->site_mail; ?> 
  <?php endif;?>

    DocumentRoot <?php print $this->root; ?> 
      
    ServerName <?php print $this->uri; ?>

    SetEnv db_type  <?php print urlencode($db_type); ?>

    SetEnv db_name  <?php print urlencode($db_name); ?>

    SetEnv db_user  <?php print urlencode($db_user); ?>

    SetEnv db_passwd  <?php print urlencode($db_passwd); ?>

    SetEnv db_host  <?php print urlencode($db_host); ?>

    SetEnv db_port  <?php print urlencode($db_port); ?>

    # Enable SSL handling.
     
    SSLEngine on

    SSLCertificateFile <?php print $ssl_cert; ?>

    SSLCertificateKeyFile <?php print $ssl_cert_key; ?>

  <?php if (!empty($ssl_chain_cert)) : ?>
    SSLCertificateChainFile <?php print $ssl_chain_cert; ?>
  <?php endif; ?>

<?php
if (sizeof($this->aliases)) {
  print "\n ServerAlias " . implode("\n ServerAlias ", $this->aliases) . "\n";
}
?>

<IfModule mod_rewrite.c>
  RewriteEngine on
<?php
if ($this->redirection) {
  // Redirect all aliases to the main https url.
  print " RewriteCond %{HTTP_HOST} !^{$this->uri}$ [NC]\n";
  print " RewriteRule ^/*(.*)$ https://{$this->uri}/$1 [NE,L,R=301]\n";
}
?>
  RewriteRule ^/files/(.*)$ /sites/<?php print $this->uri; ?>/files/$1 [L]
  RewriteCond <?php print $this->site_path; ?>/files/robots.txt -f
  RewriteRule ^/robots.txt /sites/<?php print $this->uri; ?>/files/robots.txt [L]
</IfModule>

  <?php print $extra_config; ?>

      # Error handler for Drupal > 4.6.7
      <Directory "<?php print $this->site_path; ?>/files">
        SetHandler This_is_a_Drupal_security_line_do_not_remove
      </Directory>

    # Prevent direct reading of files in the private dir.
    # This is for Drupal7 compatibility, which would normally drop
    # a .htaccess in those directories, but we explicitly ignore those
    <Directory "<?php print $this->site_path; ?>/private/" >
       SetHandler This_is_a_Drupal_security_line_do_not_remove
       Deny from all
       Options None
       Options +FollowSymLinks
    </Directory>

  </VirtualHost>
<?php endif; ?>

<?php 
  include(provision_class_directory('Provision_Config_Apache_Site') . '/vhost.tpl.php');
?>

