<?php
# This file has been generated automatically by RackTables installer.
$pdo_dsn = 'mysql:host=localhost;dbname=racktablestest';
$db_username = 'rackuser';
$db_password = 'Reset@123';

# Setting MySQL client buffer size may be required to make downloading work for
# larger files, but it does not work with mysqlnd.
# $pdo_bufsize = 50 * 1024 * 1024;
# Setting PDO SSL key, cert, and CA will allow a SSL/TLS connection to the MySQL
# DB. Make sure the files are readable by the web server
# $pdo_ssl_key = '/path/to/ssl/key'
# $pdo_ssl_cert = '/path/to/ssl/cert'
# $pdo_ssl_ca = '/path/to/ssl/ca'

$user_auth_src = 'database';
$require_local_account = TRUE;
# Default setting is to authenticate users locally, but it is possible to
# employ existing LDAP or Apache user accounts. Check RackTables wiki for
# more information, in particular, this page for LDAP configuration details:
# http://wiki.racktables.org/index.php?title=LDAP

$LDAP_options = array
(
	'server' => 'ldap://10.61.5.122',
	'domain' => '',
	'search_attr' => 'cn',
	'search_dn' => 'DC=TAFECORP,DC=COM',
# // The following credentials will be used when searching for the user's DN:
	'search_bind_rdn' => 'CN=ldapdc,CN=Users,DC=TAFECORP,DC=COM',
	'search_bind_password' => 'ld@pdc1',
	'displayname_attrs' => 'cn',
	'options' => array (LDAP_OPT_PROTOCOL_VERSION => 3),
	'use_tls' => 0,         // 0 == don't attempt, 1 == attempt, 2 == require
);

# For SAML configuration details:
# http://wiki.racktables.org/index.php?title=SAML

#$SAML_options = array
#(
#	'simplesamlphp_basedir' => '../simplesaml',
#	'sp_profile' => 'default-sp',
#	'usernameAttribute' => 'eduPersonPrincipName',
#	'fullnameAttribute' => 'fullName',
#	'groupListAttribute' => 'memberOf',
#);

# This HTML banner is intended to assist users in dispatching their issues
# to the local tech support service. Its text (in its verbatim form) will
# be appended to assorted error messages visible in user's browser (including
# "not authenticated" message). Beware of placing any sensitive information
# here, it will be readable by unauthorized visitors.
#$helpdesk_banner = '<B>This RackTables instance is supported by Example Inc. IT helpdesk, dial ext. 1234 to report a problem.</B>';

// vm2host configuration

//$vm2host_type = 1504;
//$vm2host_python = "/opt/python27/bin/python2.7";
//$vm2host_server = "172.25.5.20";
//$vm2host_username = "";
//$vm2host_password = "";
//$vm2host_hostforvmpy = "../scripts/hostForVm.py";


//The ssh binary which the plugin should use to connect to the remote syste


#$FING_ssh_binary = "/usr/bin/ssh";
#$FING_settings = array(
 #   "10.61.0.0/24" => array(
  #      "gateway" => "10.61.0.10",
   ##    "sudo" => TRUE,
   # )
#)


?>
