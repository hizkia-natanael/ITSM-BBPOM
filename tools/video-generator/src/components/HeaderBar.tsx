import React from "react";
import { staticFile } from "remotion";

export const HeaderBar: React.FC = () => {
  return (
    <div
      style={{
        position: "absolute",
        top: 24,
        left: 32,
        right: 32,
        display: "flex",
        alignItems: "center",
        justifyContent: "space-between",
        padding: "12px 24px",
        borderRadius: 20,
        backgroundColor: "rgba(15, 23, 42, 0.88)",
        backdropFilter: "blur(12px)",
        border: "1px solid rgba(255, 255, 255, 0.12)",
        boxShadow: "0 10px 25px -5px rgba(0, 0, 0, 0.4)",
        zIndex: 50,
        fontFamily: "system-ui, -apple-system, sans-serif",
      }}
    >
      <div style={{ display: "flex", alignItems: "center", gap: 16 }}>
        <img
          src={staticFile("logo-bpom.png")}
          alt="BBPOM Semarang"
          style={{ width: 36, height: 36, objectFit: "contain" }}
        />
        <div>
          <div style={{ display: "flex", alignItems: "center", gap: 8 }}>
            <span
              style={{
                fontSize: 16,
                fontWeight: 800,
                color: "#ffffff",
                letterSpacing: "-0.02em",
              }}
            >
              ELITE
            </span>
            <span
              style={{
                fontSize: 11,
                fontWeight: 600,
                color: "#34d399",
                backgroundColor: "rgba(5, 150, 105, 0.2)",
                padding: "2px 8px",
                borderRadius: 9999,
                border: "1px solid rgba(52, 211, 153, 0.3)",
              }}
            >
              BBPOM di Semarang
            </span>
          </div>
          <p style={{ margin: 0, fontSize: 12, color: "#94a3b8", fontWeight: 500 }}>
            E-Layanan IT Terintegrasi &bull; Panduan Pengajuan Layanan
          </p>
        </div>
      </div>

      <div
        style={{
          display: "flex",
          alignItems: "center",
          gap: 8,
          backgroundColor: "rgba(255, 255, 255, 0.06)",
          padding: "6px 14px",
          borderRadius: 12,
          border: "1px solid rgba(255, 255, 255, 0.08)",
        }}
      >
        <div
          style={{
            width: 8,
            height: 8,
            borderRadius: "50%",
            backgroundColor: "#10b981",
            boxShadow: "0 0 10px #10b981",
          }}
        />
        <span style={{ fontSize: 12, fontWeight: 700, color: "#e2e8f0" }}>
          Mode Panduan Interaktif
        </span>
      </div>
    </div>
  );
};
