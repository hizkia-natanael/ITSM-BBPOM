$ErrorActionPreference = "Stop"

$projectRoot = Split-Path -Parent $PSScriptRoot
$envFile = Join-Path $projectRoot ".env"

Write-Host "[1/4] Mengatur APP_URL, ASSET_URL, dan APP_PORT ke 8085 di file .env..." -ForegroundColor Cyan
if (Test-Path $envFile) {
    $envContent = [System.IO.File]::ReadAllText($envFile)
    $envContent = if ($envContent -match "(?m)^APP_URL=.*$") {
        [regex]::Replace($envContent, "(?m)^APP_URL=.*$", "APP_URL=https://lawangsewu.bbpomsemarang.id/elite")
    } else { $envContent + "`r`nAPP_URL=https://lawangsewu.bbpomsemarang.id/elite" }

    $envContent = if ($envContent -match "(?m)^ASSET_URL=.*$") {
        [regex]::Replace($envContent, "(?m)^ASSET_URL=.*$", "ASSET_URL=https://lawangsewu.bbpomsemarang.id/elite")
    } else { $envContent + "`r`nASSET_URL=https://lawangsewu.bbpomsemarang.id/elite" }

    $envContent = if ($envContent -match "(?m)^APP_PORT=.*$") {
        [regex]::Replace($envContent, "(?m)^APP_PORT=.*$", "APP_PORT=8085")
    } else { $envContent + "`r`nAPP_PORT=8085" }

    $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllText($envFile, $envContent, $utf8NoBom)
    Write-Host "      -> .env berhasil diatur ke port 8085 dan domain /elite" -ForegroundColor Green
}

$httpdConf = "C:\xampp\apache\conf\httpd.conf"
Write-Host "[2/4] Memeriksa modul Apache di httpd.conf..." -ForegroundColor Cyan
if (Test-Path $httpdConf) {
    $confText = [System.IO.File]::ReadAllText($httpdConf)
    $confChanged = $false
    foreach ($mod in @('headers_module', 'proxy_module', 'proxy_http_module')) {
        if ($confText -match "(?m)^#\s*(LoadModule\s+$mod\s+.*)$") {
            $confText = [regex]::Replace($confText, "(?m)^#\s*(LoadModule\s+$mod\s+.*)$", '$1')
            $confChanged = $true
        }
    }
    if ($confChanged) {
        $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
        [System.IO.File]::WriteAllText($httpdConf, $confText, $utf8NoBom)
        Write-Host "      -> Modul Apache (headers, proxy) berhasil diaktifkan!" -ForegroundColor Green
    }
}

$vhostFile = "C:\xampp\apache\conf\extra\httpd-vhosts.conf"
Write-Host "[3/4] Memeriksa konfigurasi Apache Reverse Proxy ($vhostFile)..." -ForegroundColor Cyan
if (Test-Path $vhostFile) {
    $vhostContent = [System.IO.File]::ReadAllText($vhostFile)
    Copy-Item $vhostFile "$vhostFile.bak" -Force

    $proxyBlock = @"

    # Aturan: Akses /ELITE, /elite, dan /helpdesk dikirim ke Docker Port 8085
    <Location "/ELITE">
        ProxyPass http://127.0.0.1:8085
        ProxyPassReverse http://127.0.0.1:8085
        ProxyPreserveHost On
        <IfModule headers_module>
            RequestHeader set X-Forwarded-Proto "https"
            RequestHeader set X-Forwarded-Port "443"
        </IfModule>
    </Location>
    <Location "/elite">
        ProxyPass http://127.0.0.1:8085
        ProxyPassReverse http://127.0.0.1:8085
        ProxyPreserveHost On
        <IfModule headers_module>
            RequestHeader set X-Forwarded-Proto "https"
            RequestHeader set X-Forwarded-Port "443"
        </IfModule>
    </Location>
    <Location "/helpdesk">
        ProxyPass http://127.0.0.1:8085
        ProxyPassReverse http://127.0.0.1:8085
        ProxyPreserveHost On
        <IfModule headers_module>
            RequestHeader set X-Forwarded-Proto "https"
            RequestHeader set X-Forwarded-Port "443"
        </IfModule>
    </Location>
</VirtualHost>
"@
    $vhostContent = [regex]::Replace($vhostContent, '(?s)\s*<Location\s+"/(?:ELITE|elite|helpdesk)">.*?</Location>', '')
    $vhostContent = $vhostContent -replace "</VirtualHost>", $proxyBlock
    $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllText($vhostFile, $vhostContent, $utf8NoBom)
    Write-Host "      -> Konfigurasi reverse proxy berhasil diperbarui!" -ForegroundColor Green
}

Write-Host "[4/4] Memverifikasi sintaks dan me-restart Apache XAMPP..." -ForegroundColor Cyan
$httpdExe = "C:\xampp\apache\bin\httpd.exe"
if (Test-Path $httpdExe) {
    $syntax = & $httpdExe -t 2>&1
    Write-Host "      -> Hasil tes konfigurasi Apache: $syntax"
    if ($syntax -match "Syntax OK") {
        $svc = Get-Service -Name "Apache2.4" -ErrorAction SilentlyContinue
        if ($svc -and $svc.Status -eq "Running") {
            Restart-Service -Name "Apache2.4" -Force
        } else {
            Get-Process -Name "httpd" -ErrorAction SilentlyContinue | Stop-Process -Force
            Start-Sleep -Seconds 1
            Start-Process -FilePath $httpdExe -WorkingDirectory "C:\xampp\apache" -WindowStyle Hidden
            Start-Sleep -Seconds 2
        }
        Write-Host "      -> Apache XAMPP berhasil di-restart!" -ForegroundColor Green
    } else {
        Write-Host "      [PERINGATAN] Ada kesalahan sintaks pada Apache! Memulihkan backup..." -ForegroundColor Red
        if (Test-Path "$vhostFile.bak") { Copy-Item "$vhostFile.bak" $vhostFile -Force }
    }
}
