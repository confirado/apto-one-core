import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { fabric } from 'fabric';
import { environment } from '@apto-frontend/src/environments/environment';

@Injectable({
  providedIn: 'root',
})
export class FabricCanvasService {
  public constructor(private http: HttpClient) {
  }

  public uploadLayerImage(fabricCanvas, printArea, renderImage, fileName, callback) {
    // create a copy from fabric original canvas
    let canvasCopyBuffer = document.createElement('canvas');
    let canvasCopy = new fabric.Canvas(canvasCopyBuffer);

    canvasCopy.loadFromJSON(JSON.stringify(fabricCanvas), () => {
      canvasCopy.setWidth(renderImage.width);
      canvasCopy.setHeight(renderImage.height);
      canvasCopy.setZoom(1);
      canvasCopy.renderAll();

      let dataUrl = canvasCopy.toDataURL({
        format: 'png',
        left: printArea.left,
        top: printArea.top,
        width: printArea.width,
        height: printArea.height
      });

      let blob = this.dataUrlToBlob(dataUrl);
      let file = this.blobToFile(blob, fileName + '.png');
      this.uploadFile(file, fileName, callback);
    });
  }

  private dataUrlToBlob(dataUrl) {
    let arr = dataUrl.split(','), mime = arr[0].match(/:(.*?);/)[1],
      bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
    while(n--){
      u8arr[n] = bstr.charCodeAt(n);
    }
    return new Blob([u8arr], {type:mime});
  }
  private blobToFile(blob, fileName){
    blob.lastModifiedDate = new Date();
    blob.name = fileName;
    return blob;
  }

  private uploadFile(file, fileName, callback) {
    const formData = new FormData();
    formData.append('file[0]', file);
    formData.append('command', 'UploadUserImageFile');
    formData.append('arguments[0]', fileName);
    formData.append('arguments[1]', 'png');
    formData.append('arguments[2]', '/apto-plugin-image-upload/render-images/2023/05');

    const upload$ = this.http.post(environment.api.command, formData);
    callback(upload$);
  }

  public updateTextElementForBending(textElement, bendValue) {
    textElement.clone((tempText) => {
      let options = {
        charSpacing: 125,
        pathAlign: 'ascender',
        path: new fabric.Path(this.describeArc(bendValue, bendValue, bendValue, 270, 450), {
          fill: "transparent",
          stroke: "transparent",
          strokeWidth: 0,
          originX: "center",
          originY: "center",
        }),
      }
      textElement.setOptions(options);
    });

    //textElement.rotate(getRotationRadius(bendValue, textElement));
    //adjustPosition(bendValue, textElement);
    //render(canvasId);
  }

  private getSpacing(spacingValue) {
    const defaultSpacing = 1;
    return defaultSpacing * spacingValue;
  }

  private getPath(tempText, bendValue) {
    // Get Variables for curve calculation
    let left = 0;
    let top = tempText.top;
    let offset = 0;
    if ((bendValue < 40 && bendValue > 0) || (bendValue > -40 && bendValue < 0)) {
      offset = 0;
    }
    let right = tempText.width + offset;
    let radius = 0;

    // Calculate Radius
    // Radius very large if no curve
    if (bendValue == 0) {
      radius = 1000 * right;
    }
    // Calculate Radius depending on slider[-100, 100]
    else {
      radius = this.getRadiusFromRange(bendValue, right);
    }

    // Change Curve side depending on positive or negative slider
    // (TODO) Maybe change this to checkbox
    // 0 = curve to top
    // 1 = curve to bottom
    let curveSide = 0;
    if (bendValue < 0) {
      curveSide = 1;
    }

    // Create SVG Path (with A - Arc)
    let path = "M " + left + " " + top + "A " + radius + " " + radius + " 0 0 " + curveSide + " " + right + " " + top;

    // Create SVG Element to get the Length of the Curve (needed for Rotation)
    let newpath = document.createElementNS("http://www.w3.org/2000/svg", "path");
    newpath.setAttributeNS(null, "d", path);
    let pathLength = newpath.getTotalLength();
    // Create Fabric Path Element
    console.error(path);

    let altPath = "M 0, 380 m -150, 0 a 150,150 0 1,1 300,0 a 150,150 0 1,1 -300,0"
    let altPath2 = "M 0 125 A 125 125 0 0 1 250 125";
    let altPath3 = "M 0 128 A 128 128 0 0 1 256 128"
    return new fabric.Path(altPath3, {
      //left: tempText.left,
      //top: tempText.top,
      //name: "path",
      fill: "transparent",
      stroke: "#ffffff",
      strokeWidth: 0,
      //width: right,
      originX: "center",
      originY: "center",
      //pathLength: pathLength
    });
  }

  // Magic Function to get a Radius from [-100,100] Slider and Textwidth
  // see https://www.geogebra.org/m/zdwpfuhn for Original Values
  // see https://www.geogebra.org/m/b9nvwd3z to create own Formula
  private getRadiusFromRange(bendValue, textWidth) {
    return textWidth * (Math.pow(0.25, 0.055 * Math.abs(bendValue) - 1.2) + 0.5);
  }

  private polarToCartesian(centerX, centerY, radius, angleInDegrees) {
    const angleInRadians = (angleInDegrees-90) * Math.PI / 180.0;

    return {
      x: centerX + (radius * Math.cos(angleInRadians)),
      y: centerY + (radius * Math.sin(angleInRadians))
    };
  }

  private describeArc(x, y, radius, startAngle, endAngle){

    const start = this.polarToCartesian(x, y, radius, startAngle);
    const end = this.polarToCartesian(x, y, radius, endAngle);

    const largeArcFlag = endAngle - startAngle <= 180 ? "0" : "1";

    return [
      "M", start.x, start.y,
      "A", radius, radius, 0, largeArcFlag, 1, end.x, end.y
    ].join(" ");
  }
}
