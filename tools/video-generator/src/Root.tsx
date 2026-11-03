import React from "react";
import { Composition } from "remotion";
import { TutorialComposition } from "./TutorialComposition";

export const Root: React.FC = () => {
  return (
    <>
      <Composition
        id="TutorialScenarioA"
        component={TutorialComposition}
        durationInFrames={1365}
        fps={30}
        width={1920}
        height={1080}
      />
    </>
  );
};
