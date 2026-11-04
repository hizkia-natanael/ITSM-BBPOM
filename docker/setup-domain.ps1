$ErrorActionPreference = "Stop"

$projectRoot = Split-Path -Parent $PSScriptRoot
$envFile = Join-Path $projectRoot ".env"

Write-Host "[1/3] Mengatur APP_URL dan ASSET_URL ke /elite di file .env..." -ForegroundColor Cyan
if (Test-Path $envFile) {
    $envContent = [System.IO.File]::ReadAllText($envFile)
    if ($envContent -match "(?m)^APP_URL=.*$") {
        $envContent = [regex]::Replace($envContent, "(?m)^APP_URL=.*$", "APP_URL=https://lawangsewu.bbpomsemarang.id/elite")
    } else {
        $envContent += "`r`nAPP_URL=https://lawangsewu.bbpomsemarang.id/elite"
    }

    if ($envContent -match "(?m)^ASSET_URL=.*$") {
        $envContent = [regex]::Replace($envContent, "(?m)^ASSET_URL=.*$", "ASSET_URL=https://lawangsewu.bbpomsemarang.id/elite")
    } else {
        $envContent += "`r`nASSET_URL=https://lawangsewu.bbpomsemarang.id/elite`r`n"
    }

    $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllText($envFile, $envContent, $utf8NoBom)
    Write-Host "      -> .env berhasil diatur ke https://lawangsewu.bbpomsemarang.id/elite" -ForegroundColor Green
}

$vhostFile = "C:\xampp\apache\conf\extra\httpd-vhosts.conf"
Write-Host "[2/3] Memeriksa konfigurasi Apache Reverse Proxy ($vhostFile)..." -ForegroundColor Cyan
if (Test-Path $vhostFile) {
    $vhostContent = [System.IO.File]::ReadAllText($vhostFile)
    $needsUpdate = $false

    if ($vhostContent -notmatch 'X-Forwarded-Proto' -or $vhostContent -notmatch '<Location\s+"/elite">') {
        $needsUpdate = $true
    }

    if ($needsUpdate) {
        Copy-Item $vhostFile "$vhostFile.bak" -Force
        $proxyBlock = @"

    # Aturan: Semua akses ke /ELITE, /elite, dan /helpdesk dikirim ke Docker Port 8085 dengan SSL Header
    <Location "/ELITE">
        ProxyPass http://127.0.0.1:8085
        ProxyPassReverse http://127.0.0.1:8085
        ProxyPreserveHost On
        RequestHeader set X-Forwarded-Proto "https"
        RequestHeader set X-Forwarded-Port "443"
    </Location>
    <Location "/elite">
        ProxyPass http://127.0.0.1:8085
        ProxyPassReverse http://127.0.0.1:8085
        ProxyPreserveHost On
        RequestHeader set X-Forwarded-Proto "https"
        RequestHeader set X-Forwarded-Port "443"
    </Location>
    <Location "/helpdesk">
        ProxyPass http://127.0.0.1:8085
        ProxyPassReverse http://127.0.0.1:8085
        ProxyPreserveHost On
        RequestHeader set X-Forwarded-Proto "https"
        RequestHeader set X-Forwarded-Port "443"
    </Location>
</VirtualHost>
"@
        $vhostContent = [regex]::Replace($vhostContent, '(?s)\s*<Location\s+"/(?:ELITE|elite|helpdesk)">.*?</Location>', '')
        $vhostContent = $vhostContent -replace "</VirtualHost>", $proxyBlock
        $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
        [System.IO.File]::WriteAllText($vhostFile, $vhostContent, $utf8NoBom)
        Write-Host "      -> Blok Apache reverse proxy dengan HTTPS headers berhasil diperbarui!" -ForegroundColor Green

        Write-Host "[3/3] Me-restart Apache XAMPP agar konfigurasi aktif..." -ForegroundColor Cyan
        $svc = Get-Service -Name "Apache2.4" -ErrorAction SilentlyContinue
        if ($svc -and $svc.Status -eq "Running") {
            Restart-Service -Name "Apache2.4" -Force
        } else {
            Get-Process -Name "httpd" -ErrorAction SilentlyContinue | Stop-Process -Force
            Start-Sleep -Seconds 1
            Start-Process -FilePath "C:\xampp\apache\bin\httpd.exe" -WorkingDirectory "C:\xampp\apache" -WindowStyle Hidden
            Start-Sleep -Seconds 2
        }
        Write-Host "      -> Apache XAMPP berhasil di-restart!" -ForegroundColor Green
    } else {
        Write-Host "      -> Konfigurasi reverse proxy Apache sudah lengkap dan terdaftar." -ForegroundColor Green
    }
} else {
    Write-Host "      -> File $vhostFile tidak ditemukan di PC ini (dilewati)." -ForegroundColor Yellow
}
