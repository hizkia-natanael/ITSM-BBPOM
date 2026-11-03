import { chromium } from "playwright";
import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const rootDir = path.resolve(__dirname, "..");

const RECORDINGS_DIR = path.join(rootDir, "out", "recordings");
fs.mkdirSync(RECORDINGS_DIR, { recursive: true });

async function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

async function runScenarioA() {
  console.log("[Playwright] Memulai perekaman browser skenario A (Pengajuan Tiket Pegawai)...");

  const startTime = Date.now();
  const timeline = [];

  function recordStep(id, title) {
    const elapsedSeconds = ((Date.now() - startTime) / 1000).toFixed(2);
    console.log(`[Timeline] Step ${id} @ ${elapsedSeconds}s - ${title}`);
    timeline.push({ id, title, start: parseFloat(elapsedSeconds) });
  }

  const browser = await chromium.launch({
    headless: true,
    args: [
      "--window-size=1920,1080",
      "--no-sandbox",
      "--disable-dev-shm-usage"
    ]
  });

  const context = await browser.newContext({
    viewport: { width: 1920, height: 1080 },
    recordVideo: {
      dir: RECORDINGS_DIR,
      size: { width: 1920, height: 1080 }
    }
  });

  const page = await context.newPage();

  try {
    // ---------------------------------------------------------
    // STEP 1: LOGIN PEGAWAI
    // ---------------------------------------------------------
    recordStep("step1_login", "Masuk ke Sistem ELITE");
    await page.goto("http://127.0.0.1:8000/login", { waitUntil: "networkidle" });
    await sleep(1500);

    // Search user
    const searchInput = page.locator('input[placeholder="Cari nama atau NIP..."]');
    await searchInput.click();
    await searchInput.pressSequentially("diah", { delay: 65 });
    await sleep(600);

    // Click the first dropdown item
    const userOption = page.locator('button:has-text("Diah Suwanti")').first();
    if (await userOption.isVisible()) {
      await userOption.click();
    } else {
      await page.keyboard.press("Enter");
    }
    await sleep(800);

    // Fill password
    const passwordInput = page.locator("#password");
    await passwordInput.click();
    await passwordInput.pressSequentially("tikbpomsmg", { delay: 50 });
    await sleep(800);

    // Click submit
    const submitLoginBtn = page.locator('button[type="submit"]');
    await submitLoginBtn.click();
    await page.waitForURL("**/dashboard", { timeout: 10000 });
    await sleep(2000);

    // ---------------------------------------------------------
    // STEP 2: DASHBOARD KE KATALOG LAYANAN
    // ---------------------------------------------------------
    recordStep("step2_dashboard", "Menuju Portal Katalog Layanan");
    await sleep(1500);

    // Navigate to tickets/create
    const createTicketLink = page.locator('a[href*="/tickets/create"]').first();
    if (await createTicketLink.isVisible()) {
      await createTicketLink.hover();
      await sleep(600);
      await createTicketLink.click();
    } else {
      await page.goto("http://127.0.0.1:8000/tickets/create");
    }

    await page.waitForURL("**/tickets/create", { timeout: 10000 });
    await sleep(2000);

    // ---------------------------------------------------------
    // STEP 3: EKSPLORASI KATALOG & PENCARIAN INSTAN
    // ---------------------------------------------------------
    recordStep("step3_catalog", "Pencarian Instan & Filter Kategori");
    
    // Type in search bar
    const searchCatalog = page.locator('input[x-model="search"]');
    await searchCatalog.click();
    await sleep(500);
    await searchCatalog.pressSequentially("zoom", { delay: 80 });
    await sleep(1800);

    // Reset search to show all
    const resetSearchBtn = page.locator('button[title="Reset"]');
    if (await resetSearchBtn.isVisible()) {
      await resetSearchBtn.click();
      await sleep(1000);
    }

    // ---------------------------------------------------------
    // STEP 4: MEMILIH LAYANAN ZOOM & PENGISIAN FORM
    // ---------------------------------------------------------
    recordStep("step4_fill_form", "Memilih Layanan Zoom & Pengisian Formulir");

    // Click Zoom Card in catalog
    const zoomCard = page.locator('h4:has-text("Zoom Meeting")').first();
    await zoomCard.scrollIntoViewIfNeeded();
    await sleep(500);
    await zoomCard.click();
    await sleep(1500);

    // Select subcategory "permintaan_link_zoom" if visible
    const linkZoomBtn = page.locator('button:has-text("Permintaan Link Zoom")').first();
    if (await linkZoomBtn.isVisible()) {
      await linkZoomBtn.click();
      await sleep(600);
    }

    // Fill form fields
    const namaAcaraInput = page.locator("#zoom_nama_acara");
    await namaAcaraInput.waitFor({ state: "visible", timeout: 5000 });
    await namaAcaraInput.click();
    await namaAcaraInput.pressSequentially("Rapat Koordinasi Evaluasi Layanan TIK BBPOM", { delay: 35 });
    await sleep(600);

    const jamInput = page.locator("#zoom_jam");
    await jamInput.click();
    await jamInput.pressSequentially("09:00 - 12:00 WIB", { delay: 35 });
    await sleep(1500);

    // ---------------------------------------------------------
    // STEP 5: SUBMIT TIKET & TRACKING STATUS
    // ---------------------------------------------------------
    recordStep("step5_tracking", "Submit Tiket & Tracking Status Real-time");

    // Submit button
    const submitTicketBtn = page.locator('button[type="submit"]:has-text("Kirim Pengaduan Tiket")');
    await submitTicketBtn.scrollIntoViewIfNeeded();
    await sleep(600);
    await submitTicketBtn.click();

    // Wait for redirect to ticket detail
    await page.waitForURL("**/tickets/*", { timeout: 15000 });
    await sleep(2000);

    // Smooth scroll down to showcase tracking and details
    await page.evaluate(() => {
      window.scrollBy({ top: 400, behavior: "smooth" });
    });
    await sleep(2000);

    await page.evaluate(() => {
      window.scrollBy({ top: 400, behavior: "smooth" });
    });
    await sleep(2000);

    console.log("[Playwright] Skenario selesai dengan sukses!");
  } catch (err) {
    console.error("[Playwright Error]", err);
  } finally {
    // Close page and context to flush video to disk
    const videoObj = page.video();
    await page.close();
    await context.close();
    await browser.close();

    if (videoObj) {
      const videoPath = await videoObj.path();
      const finalVideoPath = path.join(RECORDINGS_DIR, "scenario-a-raw.webm");
      fs.copyFileSync(videoPath, finalVideoPath);
      console.log(`[Playwright] Video rekaman tersimpan di: ${finalVideoPath}`);
    }

    const timelinePath = path.join(RECORDINGS_DIR, "timeline.json");
    fs.writeFileSync(timelinePath, JSON.stringify(timeline, null, 2), "utf-8");
    console.log(`[Playwright] Timeline tersimpan di: ${timelinePath}`);
  }
}

runScenarioA();
