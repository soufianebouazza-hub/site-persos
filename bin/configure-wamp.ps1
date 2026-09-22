$ErrorActionPreference='Stop'
$projectPath = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path.Replace('\','/')
$vhostFile='C:\wamp64\bin\apache\apache2.4.65\conf\extra\httpd-vhosts.conf'
$apacheExe='C:\wamp64\bin\apache\apache2.4.65\bin\httpd.exe'
$vhostText=Get-Content -LiteralPath $vhostFile -Raw
if ($vhostText -notmatch '# BEGIN E-VITRINE') {
    Copy-Item -LiteralPath $vhostFile -Destination ($vhostFile+'.before-evitrine.bak') -ErrorAction Stop
    $siteConfig=@"

# BEGIN E-VITRINE
Listen 127.0.0.1:4173
<VirtualHost 127.0.0.1:4173>
    ServerName 127.0.0.1
    DocumentRoot "$projectPath/public"
    SetEnv APP_ENV local
    <Directory "$projectPath/public">
        Options -Indexes -MultiViews +FollowSymLinks
        AllowOverride All
        Require local
    </Directory>
    php_admin_flag display_errors Off
    php_admin_flag log_errors On
    php_admin_flag expose_php Off
    ErrorLog "c:/wamp64/logs/evitrine-error.log"
    CustomLog "c:/wamp64/logs/evitrine-access.log" common
</VirtualHost>
# END E-VITRINE
"@
    Add-Content -LiteralPath $vhostFile -Value $siteConfig -Encoding utf8
}
& $apacheExe -t
if ($LASTEXITCODE -ne 0) { throw 'Configuration Apache invalide : services non redémarrés.' }
Start-Service wampmysqld64
Start-Service wampmariadb64
if ((Get-Service wampapache64).Status -eq 'Running') { Restart-Service wampapache64 } else { Start-Service wampapache64 }
Get-Service wampapache64,wampmysqld64,wampmariadb64 | Select-Object Name,Status
