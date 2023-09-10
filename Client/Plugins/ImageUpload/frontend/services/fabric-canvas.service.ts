import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { fabric } from 'fabric';
import { environment } from '@apto-frontend/src/environments/environment';
import { PrintArea } from '@apto-image-upload-frontend/store/canvas/canvas.model';

@Injectable({
  providedIn: 'root',
})
export class FabricCanvasService {
  public constructor(private http: HttpClient) {
  }

  public uploadLayerImage(fabricCanvas, printAreas, renderImage, fileNameId, directory, callback) {
    // create a copy from fabric original canvas
    let canvasCopyBuffer = document.createElement('canvas');
    let canvasCopy = new fabric.Canvas(canvasCopyBuffer);

    canvasCopy.loadFromJSON(JSON.stringify(fabricCanvas), () => {
      canvasCopy.setWidth(renderImage.width);
      canvasCopy.setHeight(renderImage.height);
      canvasCopy.setZoom(1);
      canvasCopy.renderAll();

      printAreas.forEach((printArea) => {
        let fileName = fileNameId + '-' + printArea.identifier;
        let dataUrl = canvasCopy.toDataURL({
          format: 'png',
          left: printArea.left,
          top: printArea.top,
          width: printArea.width,
          height: printArea.height
        });

        let blob = this.dataUrlToBlob(dataUrl);
        let file = this.blobToFile(blob, fileName + '.png');
        callback(this.uploadFile(file, fileName, 'png', directory));
      });
    });
  }

  public uploadFile(file, fileName, extension, directory) {
    const formData = new FormData();
    formData.append('file[0]', file);
    formData.append('command', 'UploadUserImageFile');
    formData.append('arguments[0]', fileName);
    formData.append('arguments[1]', extension);
    formData.append('arguments[2]', directory);

    return this.http.post(environment.api.command, formData);
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

  private describeArc(x, y, radius, startAngle, endAngle){

    const start = this.polarToCartesian(x, y, radius, startAngle);
    const end = this.polarToCartesian(x, y, radius, endAngle);

    const largeArcFlag = endAngle - startAngle <= 180 ? "0" : "1";

    return [
      "M", start.x, start.y,
      "A", radius, radius, 0, largeArcFlag, 1, end.x, end.y
    ].join(" ");
  }

  private polarToCartesian(centerX, centerY, radius, angleInDegrees) {
    const angleInRadians = (angleInDegrees-90) * Math.PI / 180.0;

    return {
      x: centerX + (radius * Math.cos(angleInRadians)),
      y: centerY + (radius * Math.sin(angleInRadians))
    };
  }
}
