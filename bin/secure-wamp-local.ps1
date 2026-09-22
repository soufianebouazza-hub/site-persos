$ErrorActionPreference='Stop'
$databases=@(
    @{Path='C:\wamp64\bin\mysql\mysql8.4.7\my.ini'; Group='wampmysqld64'},
    @{Path='C:\wamp64\bin\mariadb\mariadb11.4.9\my.ini'; Group='wampmariadb64'}
)
foreach($database in $databases){
    $configPath=$database.Path
    $configBody=Get-Content -LiteralPath $configPath -Raw
    if($configBody -notmatch '(?m)^bind-address=127\.0\.0\.1\s*$'){
        if(!(Test-Path -LiteralPath ($configPath+'.before-evitrine.bak'))){Copy-Item -LiteralPath $configPath -Destination ($configPath+'.before-evitrine.bak')}
        $groupMarker='['+$database.Group+']'
        $configBody=$configBody.Replace($groupMarker,$groupMarker+"`r`nbind-address=127.0.0.1")
        Set-Content -LiteralPath $configPath -Value $configBody -Encoding ascii
    }
}
Restart-Service wampmysqld64
Restart-Service wampmariadb64
Get-Service wampmysqld64,wampmariadb64 | Select-Object Name,Status
