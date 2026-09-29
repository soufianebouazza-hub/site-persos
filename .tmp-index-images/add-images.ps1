$ErrorActionPreference = 'Stop'
$sourceRoot = 'C:\Users\soufi\Desktop\poject_persos_site\site_persos\public'
$targetRoot = 'C:\Users\soufi\Desktop\poject_persos_site\site_test'
$indexPath = Join-Path $targetRoot 'index.html'
$html = [System.IO.File]::ReadAllText($indexPath)
if ([regex]::Matches($html, 'src=""').Count -ne 5) { throw 'Les cinq emplacements attendus ont changé.' }
$imageRoot = Join-Path $targetRoot 'images'
New-Item -ItemType Directory -Path $imageRoot -Force | Out-Null
Copy-Item -LiteralPath (Join-Path $sourceRoot 'assets\img\logo-evitrine-vert.svg') -Destination (Join-Path $imageRoot 'logo.svg')
foreach ($concept in @('atelier-bois','studio-eclat','table-saison')) {
    Copy-Item -LiteralPath (Join-Path $sourceRoot "concepts\$concept\img\apercu.webp") -Destination (Join-Path $imageRoot "$concept.webp")
}
$photo = [Convert]::ToBase64String([System.IO.File]::ReadAllBytes((Join-Path $sourceRoot 'concepts\snack-pause\img\ambiance.webp')))
$svg = @"
<svg xmlns="http://www.w3.org/2000/svg" width="1000" height="667" viewBox="0 0 1000 667">
<rect width="1000" height="667" fill="#fff8e9"/>
<rect width="1000" height="76" fill="#183d31"/>
<g font-family="Arial,sans-serif">
<text x="35" y="48" fill="#fff8e9" font-size="27" font-weight="bold">PAUSE SNACK.</text>
<g fill="#fff8e9" font-size="15"><text x="595" y="45">La carte</text><text x="707" y="45">Nous trouver</text><text x="860" y="45">Contact ↗</text></g>
<image href="data:image/webp;base64,$photo" x="480" y="76" width="520" height="445" preserveAspectRatio="xMidYMid slice"/>
<text x="35" y="144" fill="#447050" font-size="13" letter-spacing="2">LE BON GOÛT. SANS LE DÉTOUR.</text>
<g fill="#183d31" font-size="58" font-weight="bold"><text x="35" y="224">Une vraie</text><text x="35" y="292">bonne pause.</text></g>
<g fill="#415347" font-size="18"><text x="35" y="349">Burgers généreux, frites dorées</text><text x="35" y="377">et plaisir tout simple.</text></g>
<rect x="35" y="418" width="231" height="53" rx="26" fill="#df5c32"/>
<text x="63" y="450" fill="white" font-size="17" font-weight="bold">Découvrir la carte →</text>
<rect y="521" width="1000" height="52" fill="#f1c65b"/>
<text x="500" y="553" text-anchor="middle" fill="#183d31" font-size="17" font-weight="bold">BURGERS GÉNÉREUX  ✳  FRITES DORÉES  ✳  SUR PLACE OU À EMPORTER</text>
<text x="35" y="632" fill="#183d31" font-size="34" font-weight="bold">Les envies du moment.</text>
<text x="792" y="631" fill="#447050" font-size="14">Concept de site web</text>
</g></svg>
"@
[System.IO.File]::WriteAllText((Join-Path $imageRoot 'snack-pause.svg'), $svg, [System.Text.UTF8Encoding]::new($false))
$html = $html.Replace('<img src="" alt="logo de la societer">', '<img src="images/logo.svg" alt="logo de la societer" width="64" height="64">')
$names = @('atelier-bois.webp','studio-eclat.webp','table-saison.webp','snack-pause.svg')
for ($i = 0; $i -lt 4; $i++) {
    $old = '<img src="" alt="image d'' un site web ' + ($i + 1) + '">'
    $new = '<img src="images/' + $names[$i] + '" alt="image d'' un site web ' + ($i + 1) + '" width="1000" height="667" style="display: block; width: 100%; height: auto;" loading="lazy">'
    $html = $html.Replace($old, $new)
}
$html = $html.Replace("        <div>`r`n", "        <div style=`"grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr)); gap: 24px; padding: 24px;`">`r`n")
$html = $html.Replace("        <div>`n", "        <div style=`"grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr)); gap: 24px; padding: 24px;`">`n")
[System.IO.File]::WriteAllText($indexPath, $html, [System.Text.UTF8Encoding]::new($false))
foreach ($match in [regex]::Matches($html, '<img\s+src="([^"]+)"')) {
    if (-not (Test-Path -LiteralPath (Join-Path $targetRoot $match.Groups[1].Value))) { throw 'Image manquante' }
}
Write-Output 'Index mis à jour : les cinq images locales existent. Les alt sont conservés.'
