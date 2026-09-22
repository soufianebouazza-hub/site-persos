param([ValidateSet('create','password','disable')][string]$Action='create',[Parameter(Mandatory=$true)][string]$Email,[ValidateSet('admin','client')][string]$Role='client',[string]$ClientId='')
$phpPath = Join-Path $PSScriptRoot '..\.runtime\php\php.exe'
if (!(Test-Path -LiteralPath $phpPath)) { $phpPath = 'C:\wamp64\bin\php\php8.3.28\php.exe' }
if ($Action -eq 'disable') { & $phpPath (Join-Path $PSScriptRoot 'account.php') $Action $Email; exit $LASTEXITCODE }
$accountSecret = Read-Host 'Nouveau mot de passe (14 caractères minimum)' -AsSecureString
$accountPointer = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($accountSecret)
try { [Runtime.InteropServices.Marshal]::PtrToStringBSTR($accountPointer) | & $phpPath (Join-Path $PSScriptRoot 'account.php') $Action $Email $Role $ClientId }
finally { [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($accountPointer) }
