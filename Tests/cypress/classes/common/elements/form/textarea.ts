import { Attributes, ElementInterface } from '../../../interfaces/element-interface';
import Chainable = Cypress.Chainable;

export class Textarea implements ElementInterface {

  public static getByAttr(selector: string): typeof Textarea {
    cy.get(`[data-cy="${selector}"]`).as('cypressElem');
    cy.get('@cypressElem').should('exist');

    return Textarea;
  }

  public static get(selector: string): typeof Textarea {
    cy.get(selector).as('cypressElem');
    cy.get('@cypressElem').should('exist');

    return Textarea;
  }

  /**
   * Sets a custom cypress element for testing
   *
   * makes sense in cases when we don't select our element but rather we get it from search or so, then we can with this method make it as
   * testing object and apply all our methods to it
   *
   *  Checkbox.set(cy.dataCy('product-active'))
   *          .hasLabel('Aktiv')
   *          .isUnChecked();
   *
   * @param elem
   */
  public static set(elem: Chainable<JQuery<HTMLElement>>): typeof Textarea {
    elem.as('cypressElem');

    return Textarea;
  }

  public static hasLabel(label: string): typeof Textarea {
    cy.get('@cypressElem').find('label').should('contain.text', label);

    return Textarea;
  }

  public static hasNotLabel(label: string): typeof Textarea {
    cy.get('@cypressElem').find('label').should('not.contain.text', label);

    return Textarea;
  }

  public static hasValue(value: any): typeof Textarea {
    cy.get('@cypressElem').find('textarea').should('contain.value', value);

    return Textarea;
  }

  public static hasNotValue(value: any): typeof Textarea {
    cy.get('@cypressElem').find('textarea').should('not.contain.value', value);

    return Textarea;
  }

  // todo
  public static hasError(): typeof Textarea {
    return Textarea;
  }

  // todo
  public static hasNotError(): typeof Textarea {
    return Textarea;
  }

  public static attributes(attributes: Attributes): typeof Textarea {
    for(let condition in attributes) {
      if (attributes[condition] !== null) {
        cy.get('@cypressElem').should(condition, attributes[condition]);
      }
      else {
        cy.get('@cypressElem').should(condition);
      }
    }

    return Textarea;
  }
}
