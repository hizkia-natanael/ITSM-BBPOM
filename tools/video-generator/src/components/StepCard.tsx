import React from "react";
import { spring, useCurrentFrame, useVideoConfig } from "remotion";

interface StepCardProps {
  stepNumber: number;
  totalSteps: number;
  title: string;
  subtitle: string;
}

export const StepCard: React.FC<StepCardProps> = ({
  stepNumber,
  totalSteps,
  title,
  subtitle,
}) => {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();

  const entrance = spring({
    frame,
    fps,
    config: { damping: 15, mass: 0.8 },
  });

  const translateY = (1 - entrance) * 60;
  const opacity = entrance;

  return (
    <div
      style={{
        position: "absolute",
        bottom: 40,
        left: 32,
        right: 32,
        display: "flex",
        alignItems: "center",
        justifyContent: "space-between",
        padding: "18px 28px",
        borderRadius: 22,
        backgroundColor: "rgba(15, 23, 42, 0.92)",
        backdropFilter: "blur(16px)",
        border: "1px solid rgba(52, 211, 153, 0.35)",
        boxShadow: "0 20px 35px -10px rgba(0, 0, 0, 0.5)",
        transform: `translateY(${translateY}px)`,
        opacity,
        zIndex: 50,
        fontFamily: "system-ui, -apple-system, sans-serif",
      }}
    >
      <div style={{ display: "flex", alignItems: "center", gap: 20 }}>
        <div
          style={{
            width: 48,
            height: 48,
            borderRadius: 14,
            background: "linear-gradient(135deg, #059669 0%, #047857 100%)",
            color: "#ffffff",
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            fontWeight: 800,
            fontSize: 20,
            boxShadow: "0 4px 12px rgba(5, 150, 105, 0.4)",
            border: "1px solid rgba(255, 255, 255, 0.2)",
            flexShrink: 0,
          }}
        >
          {stepNumber}
        </div>
        <div>
          <div style={{ display: "flex", alignItems: "center", gap: 10, marginBottom: 4 }}>
            <span
              style={{
                fontSize: 11,
                fontWeight: 700,
                textTransform: "uppercase",
                letterSpacing: "0.08em",
                color: "#34d399",
                backgroundColor: "rgba(5, 150, 105, 0.15)",
                padding: "2px 8px",
                borderRadius: 6,
                border: "1px solid rgba(52, 211, 153, 0.3)",
              }}
            >
              Langkah {stepNumber} / {totalSteps}
            </span>
            <span style={{ fontSize: 13, fontWeight: 700, color: "#ffffff" }}>
              {title}
            </span>
          </div>
          <p style={{ margin: 0, fontSize: 13, color: "#cbd5e1", fontWeight: 500 }}>
            {subtitle}
          </p>
        </div>
      </div>

      <div style={{ display: "flex", alignItems: "center", gap: 6 }}>
        {[0.6, 1.0, 0.4, 0.8, 0.5].map((scale, i) => (
          <div
            key={i}
            style={{
              width: 3,
              height: 20 * scale,
              backgroundColor: "#10b981",
              borderRadius: 2,
              animation: "pulse 1s infinite",
            }}
          />
        ))}
      </div>
    </div>
  );
};
