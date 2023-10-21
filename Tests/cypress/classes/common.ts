import { UserRoleEnum, ViewportPresets, ViewportPresetsEnum } from './globals';

export class Common {

  public static login(role?: UserRoleEnum): void {
    // @todo implement this
  }

  public static logout(role?: UserRoleEnum): void {
    // @todo implement this
  }

  public static isUserLoggedIn(): void {
    // @todo implement this
  }

  public static get isUserLoggedInRequest(): IRequestData {
    return {
      alias: 'isUserLoggedInRequest',
      payload: { query: 'isUserLoggedInRequest' },
      endpoint: 'current-user',
    };
  }

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
}
