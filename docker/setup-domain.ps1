$ErrorActionPreference = "Stop"

$projectRoot = Split-Path -Parent $PSScriptRoot
$envFile = Join-Path $projectRoot ".env"

Write-Host "[1/3] Mengatur APP_URL dan ASSET_URL di file .env..." -ForegroundColor Cyan
if (Test-Path $envFile) {
    $envContent = [System.IO.File]::ReadAllText($envFile)
    if ($envContent -match "(?m)^APP_URL=.*$") {
        $envContent = [regex]::Replace($envContent, "(?m)^APP_URL=.*$", "APP_URL=https://lawangsewu.bbpomsemarang.id/helpdesk")
    } else {
        $envContent += "`r`nAPP_URL=https://lawangsewu.bbpomsemarang.id/helpdesk"
    }

    if ($envContent -match "(?m)^ASSET_URL=.*$") {
        $envContent = [regex]::Replace($envContent, "(?m)^ASSET_URL=.*$", "ASSET_URL=https://lawangsewu.bbpomsemarang.id/helpdesk")
    } else {
        $envContent += "`r`nASSET_URL=https://lawangsewu.bbpomsemarang.id/helpdesk`r`n"
    }

    $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllText($envFile, $envContent, $utf8NoBom)
    Write-Host "      -> .env berhasil diatur ke https://lawangsewu.bbpomsemarang.id/helpdesk" -ForegroundColor Green
}

$vhostFile = "C:\xampp\apache\conf\extra\httpd-vhosts.conf"
Write-Host "[2/3] Memeriksa konfigurasi Apache Reverse Proxy ($vhostFile)..." -ForegroundColor Cyan
if (Test-Path $vhostFile) {
    $vhostContent = [System.IO.File]::ReadAllText($vhostFile)
    if ($vhostContent -notmatch '<Location\s+"/helpdesk">') {
        Copy-Item $vhostFile "$vhostFile.bak" -Force
        $proxyBlock = @"

    # Aturan: Semua akses ke /helpdesk dikirim ke Docker Port 8085
    <Location "/helpdesk">
        ProxyPass http://127.0.0.1:8085
        ProxyPassReverse http://127.0.0.1:8085
    </Location>
</VirtualHost>
"@
        $vhostContent = $vhostContent -replace "</VirtualHost>", $proxyBlock
        $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
        [System.IO.File]::WriteAllText($vhostFile, $vhostContent, $utf8NoBom)
        Write-Host "      -> Blok <Location '/helpdesk'> berhasil ditambahkan ke httpd-vhosts.conf!" -ForegroundColor Green

        Write-Host "[3/3] Me-restart Apache XAMPP agar rute /helpdesk aktif..." -ForegroundColor Cyan
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
        Write-Host "      -> Blok <Location '/helpdesk'> sudah terdaftar di httpd-vhosts.conf." -ForegroundColor Green
    }
} else {
    Write-Host "      -> File $vhostFile tidak ditemukan di PC ini (dilewati)." -ForegroundColor Yellow
}
