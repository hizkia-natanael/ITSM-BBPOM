import { EdgeTTS } from "node-edge-tts";
import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const rootDir = path.resolve(__dirname, "..");

async function generateAllTTS() {
  const scenarioPath = path.join(rootDir, "content", "scenario-a.json");
  const rawData = fs.readFileSync(scenarioPath, "utf-8");
  const scenario = JSON.parse(rawData);

  const audioDir = path.join(rootDir, "out", "audio");
  fs.mkdirSync(audioDir, { recursive: true });

  const voice = scenario.voice || "id-ID-ArdiNeural";
  console.log(`[TTS] Memulai pembuatan audio narasi Bahasa Indonesia (${voice})...`);

  const manifest = [];
  const tts = new EdgeTTS({ voice });

  for (const step of scenario.steps) {
    const audioFileName = `${step.id}.mp3`;
    const audioFilePath = path.join(audioDir, audioFileName);

    console.log(`[TTS] Generating: ${step.title}...`);
    await tts.ttsPromise(step.narration, audioFilePath);

    manifest.push({
      id: step.id,
      title: step.title,
      subtitle: step.subtitle,
      narration: step.narration,
      audioFile: audioFilePath,
      relativeAudioFile: `audio/${audioFileName}`
    });
  }

  const manifestPath = path.join(audioDir, "manifest.json");
  fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2), "utf-8");
  console.log(`[TTS] Selesai! Seluruh narasi tersimpan di: ${audioDir}`);
}

generateAllTTS().catch((err) => {
  console.error("[TTS Error]", err);
  process.exit(1);
});
