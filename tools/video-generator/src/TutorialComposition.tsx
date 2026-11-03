import React from "react";
import { AbsoluteFill, Audio, OffthreadVideo, Sequence, staticFile } from "remotion";
import { HeaderBar } from "./components/HeaderBar";
import { StepCard } from "./components/StepCard";
import { ProgressBar } from "./components/ProgressBar";

interface StepConfig {
  id: string;
  stepNumber: number;
  title: string;
  subtitle: string;
  startFrame: number;
  durationFrames: number;
  audio: string;
}

const STEPS: StepConfig[] = [
  {
    id: "step1_login",
    stepNumber: 1,
    title: "Masuk ke Sistem ELITE",
    subtitle: "Autentikasi menggunakan akun resmi pegawai BBPOM",
    startFrame: 37,
    durationFrames: 491,
    audio: "audio/step1_login.mp3",
  },
  {
    id: "step2_dashboard",
    stepNumber: 2,
    title: "Menuju Katalog Layanan",
    subtitle: "Akses menu pengajuan tiket dari dashboard pegawai",
    startFrame: 528,
    durationFrames: 156,
    audio: "audio/step2_dashboard.mp3",
  },
  {
    id: "step3_catalog",
    stepNumber: 3,
    title: "Eksplorasi 12 Katalog Layanan",
    subtitle: "Pencarian instan dan filter kategori terintegrasi",
    startFrame: 684,
    durationFrames: 129,
    audio: "audio/step3_catalog.mp3",
  },
  {
    id: "step4_fill_form",
    stepNumber: 4,
    title: "Pengisian Formulir Permohonan",
    subtitle: "Formulir cerdas dengan metadata pemohon otomatis",
    startFrame: 813,
    durationFrames: 251,
    audio: "audio/step4_fill_form.mp3",
  },
  {
    id: "step5_tracking",
    stepNumber: 5,
    title: "Bukti Tiket & Tracking Status",
    subtitle: "Pemantauan pengerjaan teknisi dan transparansi SLA",
    startFrame: 1064,
    durationFrames: 301,
    audio: "audio/step5_tracking.mp3",
  },
];

export const TutorialComposition: React.FC = () => {
  return (
    <AbsoluteFill style={{ backgroundColor: "#020617" }}>
      {/* 1. Base Video Track */}
      <OffthreadVideo
        src={staticFile("scenario-a-raw.webm")}
        style={{
          width: "100%",
          height: "100%",
          objectFit: "cover",
        }}
      />

      {/* 2. Top Header Branding */}
      <HeaderBar />

      {/* 3. Steps Audio & Step Lower-Third Overlays */}
      {STEPS.map((step) => (
        <Sequence
          key={step.id}
          from={step.startFrame}
          durationInFrames={step.durationFrames}
        >
          <Audio src={staticFile(step.audio)} volume={1.0} />
          <StepCard
            stepNumber={step.stepNumber}
            totalSteps={STEPS.length}
            title={step.title}
            subtitle={step.subtitle}
          />
        </Sequence>
      ))}

      {/* 4. Bottom Progress Bar */}
      <ProgressBar />
    </AbsoluteFill>
  );
};
