import { ViewportPresets, ViewportPresetsEnum } from '../globals';
import { Interception } from 'cypress/types/net-stubbing';
import { RequestHandler } from '../requestHandler';

export class Core {

  /**
   * Checks if image is correctly loaded
   *
   * @param img
   */
  public static isImageLoadedCheck(img: Cypress.ObjectLike): void {
    // when an image link is broken (for example, image not found on server) naturalWidth is 0
    expect(img[0].naturalWidth).to.be.greaterThan(0);
  }

  public static switchViewport(preset: ViewportPresetsEnum): void {
    cy.viewport(ViewportPresets[preset].width, ViewportPresets[preset].height);
  }

  /**
   * Checks if the link is broken by sending a request to it and checking the response status
   *
   * @param link
   */
  public static isLinkBrokenTest(link: string): void {
    cy.request(link)
      .then((response) => {
        expect(response.status).to.be.within(200, 299);
      });
  }

  /**
   * html element can have no children but can have some comments, we check here for that
   *
   * @param text
   */
  public static isElementEmpty(text: string): void {
    expect(text.trim()).to.equal('');
  }

  public static checkResponsesForError(responses: Interception[]): void {
    responses.forEach(($query) => {
      expect(RequestHandler.hasResponseError($query)).to.equal(false);
    });
  }
}
