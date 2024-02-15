import { ElementInterface } from '../../../interfaces/element-interface';

export class TranslatedValue implements ElementInterface{

  private static initialSelector: string;

  public static getByAttr(selector: string): typeof TranslatedValue {
    TranslatedValue.initialSelector = `[data-cy="${selector}"]`;
    cy.get(TranslatedValue.initialSelector).should('exist');

    return TranslatedValue;
  }

  public static get(selector: string): typeof TranslatedValue {
    TranslatedValue.initialSelector = selector;
    cy.get(TranslatedValue.initialSelector).should('exist');

    return TranslatedValue;
  }

  public static hasValue(value: any): typeof TranslatedValue {
    cy.get(TranslatedValue.initialSelector).find('input').should('have.value', value);

    return TranslatedValue;
  }

  public static hasNotValue(value: any): typeof TranslatedValue {
    cy.get(TranslatedValue.initialSelector).find('input').should('not.have.value', value);

    return TranslatedValue;
  }

  public static writeValue(value: string | number | string[]): typeof TranslatedValue {
    cy.get(TranslatedValue.initialSelector).find('input, textarea').clear().type(value.toString());

    return TranslatedValue;
  }

  // todo add methods for selecting language
}
