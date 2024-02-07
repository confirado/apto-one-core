import { Attributes, Element } from './element.interface';

export class Checkbox implements Element {
  private static initialSelector: string;

  public static getByAttr(selector: string): typeof Checkbox {
    Checkbox.initialSelector = `[data-cy="${selector}"]`;
    cy.get(Checkbox.initialSelector).should('exist');

    return Checkbox;
  }

  public static get(selector: string): typeof Checkbox {
    Checkbox.initialSelector = selector;
    cy.get(Checkbox.initialSelector).should('exist');

    return Checkbox;
  }

  public static hasLabel(label: string): typeof Checkbox {
    cy.get(Checkbox.initialSelector).find('.md-label').should('contain.text', label);

    return Checkbox;
  }

  public static hasNotLabel(label: string): typeof Checkbox {
    cy.get(Checkbox.initialSelector).find('.md-label').should('not.contain.text', label);

    return Checkbox;
  }

  public static hasValue(value: any): typeof Checkbox {
    return Checkbox;
  }

  public static hasNotValue(value: any): typeof Checkbox {
    return Checkbox;
  }

  public static hasError(): typeof Checkbox {
    return Checkbox;
  }

  public static hasNotError(): typeof Checkbox {
    return Checkbox;
  }

  public static attributes(attributes: Attributes): typeof Checkbox {

    for(let condition in attributes) {
      if (attributes[condition] !== null) {
        cy.get(Checkbox.initialSelector).should(condition, attributes[condition]);
      }
      else {
        cy.get(Checkbox.initialSelector).should(condition);
      }
    }

    return Checkbox;
  }


  // unique to checkbox methods

  public static checked(): typeof Checkbox {
    cy.get(Checkbox.initialSelector).find('md-checkbox').should('have.class', 'md-checked');

    return Checkbox;
  }

  public static unChecked(): typeof Checkbox {
    cy.get(Checkbox.initialSelector).find('md-checkbox').should('have.not.class', 'md-checked');

    return Checkbox;
  }
}
