// for Chainer type check cypress.d.ts
export interface Attributes {
  [key: string]: string;
}

/**
 * Attributes can be key value:
 *    {'have.class': 'md-primary'}
 * or just key in that case we pass null
 *    {'be.visible', null}
 */
export class Material {

  /**
   * Example call:
   *
   * Material.checkbox('[data-cy="product-active"]', false, 'Aktiv', {'have.attr', 'required'});
   *
   * @param selector
   * @param checkboxState true if you check for checked condition, otherwise false
   * @param label
   * @param attributes
   */
  public static checkbox(selector: string, checkboxState: boolean, label: string|null, attributes: Attributes|null = null): void {
    const classToCheck = checkboxState ? 'have.class' : 'have.not.class';

    cy.get(selector).find('md-checkbox').should(classToCheck, 'md-checked');
    cy.get(selector).find('.md-label').should('contain.text', label);

    if (attributes) {
      for(let condition in attributes) {
        cy.get(selector).should(condition, attributes[condition]);
      }
    }
  }

  /**
   * Example call:
   *
   * Material.input('[data-cy="product-article-number"]', '', 'Artikelnummer:', {'have.attr', 'required'});
   *
   * @param selector
   * @param value
   * @param label
   * @param attributes
   */
  public static input(selector: string, value: any, label: string|null, attributes: Attributes|null = null): void {
    if (label) {
      cy.get(selector).find('label').should('have.text', label);
    }

    if (attributes) {
      for(let condition in attributes) {
        if (attributes[condition]) {
          cy.get(selector).should(condition, attributes[condition]);
        }
        else {
          cy.get(selector).should(condition);
        }
      }
    }

    cy.get(selector).find('input').should('have.value', value);
  }

  /**
   * Example call:
   *
   * Material.textarea('[data-cy="product-article-number"]', '', 'Artikelnummer:', {'have.attr', 'required'});
   *
   * @param selector
   * @param value
   * @param label
   * @param attributes
   */
  public static textarea(selector: string, value: any, label: string|null, attributes: Attributes|null = null): void {
    if (label) {
      cy.get(selector).find('label').should('have.text', label);
    }

    if (attributes) {
      for(let condition in attributes) {
        if (attributes[condition]) {
          cy.get(selector).should(condition, attributes[condition]);
        }
        else {
          cy.get(selector).should(condition);
        }
      }
    }

    cy.get(selector).find('textarea').should('have.value', value);
  }

  /**
   * md-select
   *
   * Checks for select's value and label
   *
   * @param selector
   * @param value
   * @param label
   * @param attributes
   */
  public static select(selector: string, value: any, label: string|null, attributes: Attributes|null = null): void {
    cy.get(selector).find('label').should('have.text', label);

    if (value !== '') {
      cy.get(selector)
        .find('md-select md-select-value')
        .find('.md-text')
        .should('contain.text', value);
    } else {
      // if no preselected value then .md-text does not exist
      cy.get(selector)
        .find('md-select md-select-value')
        .should(($span) => {
          const foundSpan = $span.find('.md-text');
          expect(foundSpan).to.have.length(0); // Assert that no span with class 'some-class' is found
        });
    }

    if (attributes) {
      for(let condition in attributes) {
        if (attributes[condition]) {
          cy.get(selector).find('md-select').should(condition, attributes[condition]);
        }
        else {
          cy.get(selector).find('md-select').should(condition);
        }
      }
    }
  }

  /**
   * md-button
   *
   * Material.button('[data-cy="dialog-actions_button-new-save"]', 'Speichern', {'be.visible': null, 'have.class': 'md-primary'});
   *
   * @param selector
   * @param text
   * @param attributes
   */
  public static button(selector: string, text: string|null, attributes: Attributes|null = null): void {
    if (text) {
      cy.get(selector).should('contain.text', text);
    }

    if (attributes) {
      for(let condition in attributes) {
        if (attributes[condition]) {
          cy.get(selector).should(condition, attributes[condition]);
        }
        else {
          cy.get(selector).should(condition);
        }
      }
    }
  }
}
