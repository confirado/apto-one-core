import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { fabric } from 'fabric';
import { environment } from '@apto-frontend/src/environments/environment';
import { MessageBusResponse } from '@apto-base-core/models/message-bus-response';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class FabricCanvasService {
  public constructor(private http: HttpClient) {
  }

  public uploadLayerImageForArea(fabricCanvas, printArea, renderImage, fileNameId: string, directory: string): Promise<MessageBusResponse<null>> {
    // create a copy from fabric original canvas
    const canvasCopyBuffer = document.createElement('canvas');
    const canvasCopy = new fabric.Canvas(canvasCopyBuffer);

    return new Promise(resolve => {
      canvasCopy.loadFromJSON(JSON.stringify(fabricCanvas), () => {
        canvasCopy.setWidth(renderImage.width);
        canvasCopy.setHeight(renderImage.height);
        canvasCopy.setZoom(1);
        canvasCopy.renderAll();

        const fileName = fileNameId + '-' + printArea.identifier;
        const dataUrl = canvasCopy.toDataURL({
          format: 'png',
          left: printArea.left,
          top: printArea.top,
          width: printArea.width,
          height: printArea.height
        });

        const blob = this.dataUrlToBlob(dataUrl);
        const file = this.blobToFile(blob, fileName + '.png');

        this.uploadFile(file, fileName, 'png', directory).subscribe((result) => {
          return resolve(result);
        });
      });
    });
  }

  public uploadFile(file, fileName, extension, directory): Observable<MessageBusResponse<null>> {
    const formData = new FormData();
    formData.append('file[0]', file);
    formData.append('command', 'UploadUserImageFile');
    formData.append('arguments[0]', fileName);
    formData.append('arguments[1]', extension);
    formData.append('arguments[2]', directory);

    return this.http.post<MessageBusResponse<null>>(environment.api.command, formData);
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
