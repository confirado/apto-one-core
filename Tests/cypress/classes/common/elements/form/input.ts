import { Attributes, ElementInterface } from '../../../interfaces/element-interface';
import Chainable = Cypress.Chainable;

export class Input implements ElementInterface {

  public static getByAttr(selector: string): typeof Input {
    cy.get(`[data-cy="${selector}"]`).as('cypressElem');
    cy.get('@cypressElem').should('exist');

    return Input;
  }

  public static get(selector: string): typeof Input {
    cy.get(selector).as('cypressElem');
    cy.get('@cypressElem').should('exist');

    return Input;
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
  public static set(elem: Chainable<JQuery<HTMLElement>>): typeof Input {
    elem.as('cypressElem');

    return Input;
  }

  public static hasLabel(label: string): typeof Input {
    cy.get('@cypressElem').find('label').should('contain.text', label);

    return Input;
  }

  public static hasNotLabel(label: string): typeof Input {
    cy.get('@cypressElem').find('label').should('not.contain.text', label);

    return Input;
  }

  public static hasValue(value: any): typeof Input {
    cy.get('@cypressElem').find('input').should('have.value', value);

    return Input;
  }

  public static hasNotValue(value: any): typeof Input {
    cy.get('@cypressElem').find('input').should('not.have.value', value);

    return Input;
  }

  public static hasError(): typeof Input {
    return Input;
  }

  public static hasNotError(): typeof Input {
    return Input;
  }

  public static attributes(attributes: Attributes): typeof Input {
    for(let condition in attributes) {
      if (attributes[condition] !== null) {
        cy.get('@cypressElem').find('input').should(condition, attributes[condition]);
      }
      else {
        cy.get('@cypressElem').find('input').should(condition);
      }
    }

    return Input;
  }

  // custom methods

  /**
   * This returns value not Input !!!
   */
  public static getValue() {
    return cy.get('@cypressElem').find('input').invoke('val');
  }

  public static writeValue(value: string | number | string[]): typeof Input {
    cy.get('@cypressElem').find('input').clear().type(value.toString());

    return Input;
  }

}
