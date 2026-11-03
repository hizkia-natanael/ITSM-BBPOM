import React from "react";
import { useCurrentFrame, useVideoConfig } from "remotion";

export const ProgressBar: React.FC = () => {
  const frame = useCurrentFrame();
  const { durationInFrames } = useVideoConfig();

  const progress = Math.min(1, Math.max(0, frame / durationInFrames));

  return (
    <div
      style={{
        position: "absolute",
        bottom: 0,
        left: 0,
        right: 0,
        height: 5,
        backgroundColor: "rgba(255, 255, 255, 0.15)",
        zIndex: 60,
      }}
    >
      <div
        style={{
          height: "100%",
          width: `${progress * 100}%`,
          background: "linear-gradient(90deg, #059669 0%, #10b981 50%, #34d399 100%)",
          boxShadow: "0 0 10px #10b981",
        }}
      />
    </div>
  );
};
