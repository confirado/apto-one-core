export class Tabs {
  private static initialSelector: string;

  public static getByAttr(selector: string): typeof Tabs {
    Tabs.initialSelector = `[data-cy="${selector}"]`;
    cy.get(Tabs.initialSelector).should('exist');

    return Tabs;
  }

  public static get(selector: string): typeof Tabs {
    Tabs.initialSelector = selector;
    cy.get(Tabs.initialSelector).should('exist');

    return Tabs;
  }

  public static hasContent(): typeof Tabs {
    cy.get('md-tabs-content-wrapper').should('exist');

    return Tabs;
  }

  public static select(name: string): typeof Tabs {
    cy.get(Tabs.initialSelector).within(() => {
      cy.get('md-tabs-wrapper').within(() => {
        cy.get('md-tabs-canvas').within(($mdCanvas) => {
          cy.wrap($mdCanvas).find('md-tab-item').each(($elm) => {
            if ($elm.text() === name || $elm.text() === name.toUpperCase()) {
              cy.wrap($elm).click({force: true});
            }
          });
        });
      });
    });

    return Tabs;
  }
}
