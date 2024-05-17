const modelViewer = document.querySelector("#superAR");
const rulerButton = document.querySelector("#rulerButton");

document.querySelectorAll("#superAR button").forEach(element => {
  if (!element.classList.contains("ar-button") && !element.classList.contains("close-button")) {
    element.classList.add("hide");
  }
});
document.querySelector("#dimLines").classList.add("hide");

function setVisibility(element) {
  if (element === rulerButton || element.classList.contains("ar-button") || element.classList.contains("close-button")) {
    return; 
  }
  element.classList.toggle("hide");
}

if (rulerButton) {
  rulerButton.addEventListener("click", function () {
    setVisibility(modelViewer.querySelector("#dimLines"));
    modelViewer.querySelectorAll("button").forEach((hotspot) => {
      setVisibility(hotspot);
    });
  });
}

function drawLine(svgLine, dotHotspot1, dotHotspot2, dimensionHotspot) {
  if (dotHotspot1 && dotHotspot2) {
    svgLine.setAttribute("x1", dotHotspot1.canvasPosition.x);
    svgLine.setAttribute("y1", dotHotspot1.canvasPosition.y);
    svgLine.setAttribute("x2", dotHotspot2.canvasPosition.x);
    svgLine.setAttribute("y2", dotHotspot2.canvasPosition.y);

    if (dimensionHotspot && !dimensionHotspot.facingCamera) {
      svgLine.classList.add("hide");
    } else {
      svgLine.classList.remove("hide");
    }
  }
}

const dimLines = modelViewer.querySelectorAll("line");

const renderSVG = () => {
  const hotspots = [
    { line: dimLines[0], dot1: "hotspot-dot+X-Y+Z", dot2: "hotspot-dot+X-Y-Z", dim: "hotspot-dim+X-Y" },
    { line: dimLines[1], dot1: "hotspot-dot+X-Y-Z", dot2: "hotspot-dot+X+Y-Z", dim: "hotspot-dim+X-Z" },
    { line: dimLines[2], dot1: "hotspot-dot+X+Y-Z", dot2: "hotspot-dot-X+Y-Z" },
    { line: dimLines[3], dot1: "hotspot-dot-X+Y-Z", dot2: "hotspot-dot-X-Y-Z", dim: "hotspot-dim-X-Z" },
    { line: dimLines[4], dot1: "hotspot-dot-X-Y-Z", dot2: "hotspot-dot-X-Y+Z", dim: "hotspot-dim-X-Y" }
  ];
  
  hotspots.forEach(hotspot => {
    drawLine(
      hotspot.line,
      modelViewer.queryHotspot(hotspot.dot1),
      modelViewer.queryHotspot(hotspot.dot2),
      hotspot.dim ? modelViewer.queryHotspot(hotspot.dim) : null
    );
  });
};

modelViewer.addEventListener("camera-change", renderSVG);

modelViewer.addEventListener("load", () => {
  const center = modelViewer.getBoundingBoxCenter();
  const size = modelViewer.getDimensions();
  const x2 = size.x / 2;
  const y2 = size.y / 2;
  const z2 = size.z / 2;

  const hotspots = [
    { name: "hotspot-dot+X-Y+Z", position: `${center.x + x2} ${center.y - y2} ${center.z + z2}` },
    { name: "hotspot-dim+X-Y", position: `${center.x + x2 * 1.2} ${center.y - y2 * 1.1} ${center.z}`, text: `${(size.z * 100).toFixed(0)} cm` },
    { name: "hotspot-dot+X-Y-Z", position: `${center.x + x2} ${center.y - y2} ${center.z - z2}` },
    { name: "hotspot-dim+X-Z", position: `${center.x + x2 * 1.2} ${center.y} ${center.z - z2 * 1.2}`, text: `${(size.y * 100).toFixed(0)} cm` },
    { name: "hotspot-dot+X+Y-Z", position: `${center.x + x2} ${center.y + y2} ${center.z - z2}` },
    { name: "hotspot-dim+Y-Z", position: `${center.x} ${center.y + y2 * 1.1} ${center.z - z2 * 1.1}`, text: `${(size.x * 100).toFixed(0)} cm` },
    { name: "hotspot-dot-X+Y-Z", position: `${center.x - x2} ${center.y + y2} ${center.z - z2}` },
    { name: "hotspot-dim-X-Z", position: `${center.x - x2 * 1.2} ${center.y} ${center.z - z2 * 1.2}`, text: `${(size.y * 100).toFixed(0)} cm` },
    { name: "hotspot-dot-X-Y-Z", position: `${center.x - x2} ${center.y - y2} ${center.z - z2}` },
    { name: "hotspot-dim-X-Y", position: `${center.x - x2 * 1.2} ${center.y - y2 * 1.1} ${center.z}`, text: `${(size.z * 100).toFixed(0)} cm` },
    { name: "hotspot-dot-X-Y+Z", position: `${center.x - x2} ${center.y - y2} ${center.z + z2}` }
  ];

  hotspots.forEach(hotspot => {
    modelViewer.updateHotspot({ name: hotspot.name, position: hotspot.position });
    if (hotspot.text) {
      modelViewer.querySelector(`button[slot="${hotspot.name}"]`).textContent = hotspot.text;
    }
  });

  renderSVG();
});
