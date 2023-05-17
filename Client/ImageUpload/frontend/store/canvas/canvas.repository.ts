import { Injectable } from "@angular/core";
import { map } from "rxjs";
import { MessageBusService } from "@apto-base-core/services/message-bus.service";

@Injectable({
  providedIn: 'root',
})
export class CanvasRepository {
  constructor(private messageBus: MessageBusService) {
  }

  findEditableRenderImage(state, productId, perspective, renderImageHash): any {
    return this.messageBus.query('ImageUploadFindEditableRenderImage', [state, productId, perspective, renderImageHash]).pipe(
      map(response => {
        return response.result;
      })
    );
  }
}
