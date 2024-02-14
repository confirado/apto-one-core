import { Attributes, ElementInterface } from '../../../interfaces/element-interface';

export class Textarea implements ElementInterface {
  private static initialSelector: string;

  public static getByAttr(selector: string): typeof Textarea {
    Textarea.initialSelector = `[data-cy="${selector}"]`;
    cy.get(Textarea.initialSelector).should('exist');

    return Textarea;
  }

  public static get(selector: string): typeof Textarea {
    Textarea.initialSelector = selector;
    cy.get(Textarea.initialSelector).should('exist');

    return Textarea;
  }

  public static hasLabel(label: string): typeof Textarea {
    cy.get(Textarea.initialSelector).find('label').should('contain.text', label);

    return Textarea;
  }

  public static hasNotLabel(label: string): typeof Textarea {
    cy.get(Textarea.initialSelector).find('label').should('not.contain.text', label);

    return Textarea;
  }

  public static hasValue(value: any): typeof Textarea {
    cy.get(Textarea.initialSelector).find('textarea').should('contain.value', value);

    return Textarea;
  }

  public static hasNotValue(value: any): typeof Textarea {
    cy.get(Textarea.initialSelector).find('textarea').should('not.contain.value', value);

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
        cy.get(Textarea.initialSelector).should(condition, attributes[condition]);
      }
      else {
        cy.get(Textarea.initialSelector).should(condition);
      }
    }

    return Textarea;
  }
}
