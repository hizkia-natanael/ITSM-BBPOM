import { execSync } from "child_process";
import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const rootDir = path.resolve(__dirname, "..");

async function checkServer() {
  try {
    const res = await fetch("http://127.0.0.1:8000/login");
    return res.status === 200;
  } catch (e) {
    return false;
  }
}

async function main() {
  console.log("==================================================");
  console.log("  ELITE BBPOM - Automated Tutorial Video Pipeline ");
  console.log("==================================================");

  // 1. Check Laravel server
  console.log("[1/5] Memeriksa status server ELITE (localhost:8000)...");
  const serverUp = await checkServer();
  if (!serverUp) {
    console.error("[ERROR] Server ELITE belum aktif! Jalankan 'php artisan serve' terlebih dahulu.");
    process.exit(1);
  }
  console.log("[OK] Server ELITE aktif!");

  // 2. Generate Audio TTS
  console.log("\n[2/5] Menghasilkan narasi suara Bahasa Indonesia via Edge-TTS...");
  execSync("node scripts/generate-tts.js", { stdio: "inherit", cwd: rootDir });

  // 3. Capture Screen & Actions via Playwright
  console.log("\n[3/5] Menjalankan otomasi browser & merekam alur via Playwright...");
  execSync("node scripts/capture.js", { stdio: "inherit", cwd: rootDir });

  // 4. Sync assets to public
  console.log("\n[4/5] Menyinkronkan rekaman layar dan audio ke workspace Remotion...");
  const pubDir = path.join(rootDir, "public");
  const pubAudioDir = path.join(pubDir, "audio");
  fs.mkdirSync(pubAudioDir, { recursive: true });

  fs.copyFileSync(
    path.join(rootDir, "out", "recordings", "scenario-a-raw.webm"),
    path.join(pubDir, "scenario-a-raw.webm")
  );

  const audioFiles = fs
    .readdirSync(path.join(rootDir, "out", "audio"))
    .filter((f) => f.endsWith(".mp3"));
  for (const f of audioFiles) {
    fs.copyFileSync(path.join(rootDir, "out", "audio", f), path.join(pubAudioDir, f));
  }

  // 5. Render Video via Remotion
  console.log("\n[5/5] Merender video MP4 final dengan Remotion...");
  execSync("npx remotion render src/index.ts TutorialScenarioA out/tutorial-pengajuan-tiket.mp4", {
    stdio: "inherit",
    cwd: rootDir,
  });

  console.log("\n==================================================");
  console.log("  SELESAI! Video tutorial berhasil dirender:");
  console.log("  " + path.join(rootDir, "out", "tutorial-pengajuan-tiket.mp4"));
  console.log("==================================================");
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
