import { Attributes, Element } from './element.interface';

export class Input implements Element {
  private static initialSelector: string;

  public static getByAttr(selector: string): typeof Input {
    Input.initialSelector = `[data-cy="${selector}"]`;
    cy.get(Input.initialSelector).should('exist');

    return Input;
  }

  public static get(selector: string): typeof Input {
    Input.initialSelector = selector;
    cy.get(Input.initialSelector).should('exist');

    return Input;
  }

  public static hasLabel(label: string): typeof Input {
    cy.get(Input.initialSelector).find('label').should('contain.text', label);

    return Input;
  }

  public static hasNotLabel(label: string): typeof Input {
    cy.get(Input.initialSelector).find('label').should('not.contain.text', label);

    return Input;
  }

  public static hasValue(value: any): typeof Input {
    cy.get(Input.initialSelector).find('input').should('have.value', value);

    return Input;
  }

  public static hasNotValue(value: any): typeof Input {
    cy.get(Input.initialSelector).find('input').should('not.have.value', value);

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
        cy.get(Input.initialSelector).should(condition, attributes[condition]);
      }
      else {
        cy.get(Input.initialSelector).should(condition);
      }
    }

    return Input;
  }

  // custom methods

  /**
   * This returns value not Input !!!
   */
  public static getValue() {
    return cy.get(Input.initialSelector).find('input').invoke('val');
  }

  public static writeValue(value: string | number | string[]): typeof Input {
    cy.get(Input.initialSelector).find('input').clear().type(value.toString());

    return Input;
  }

}
