import { Injectable } from "@angular/core";
import { map, Observable } from "rxjs";
import { MessageBusService } from "@apto-base-core/services/message-bus.service";
import { ContentSnippet } from "@apto-base-frontend/store/content-snippets/content-snippet.model";

@Injectable()
export class ContentSnippetRepository {
  constructor(private messageBus: MessageBusService) {
  }

  findContentSnippetTree(): Observable<ContentSnippet[]> {
    return this.messageBus.query('FindContentSnippetTree', [true, true]).pipe(
      map(response => this.responseToContentSnippet(response.result))
    );
  }

  private responseToContentSnippet(response: any): ContentSnippet[] {
    let contentSnippets: ContentSnippet[] = [];
    response.forEach((contentSnippet: any) => {
      contentSnippets.push({
        name: contentSnippet.name,
        content: contentSnippet.content,
        children: contentSnippet.children
      });
    });
    return contentSnippets;
  }
}
