import { ElementInterface } from '../../../interfaces/element-interface';

export enum TranslatedValueTypes {
  INPUT = 'input',
  TEXTAREA = 'textarea',
}

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

  public static hasLabel(value: any): typeof TranslatedValue {
    cy.get(TranslatedValue.initialSelector).find('apto-translated-value').find('label').should('contain.text', value);

    return TranslatedValue;
  }

  public static hasNotLabel(value: any): typeof TranslatedValue {
    cy.get(TranslatedValue.initialSelector).find('apto-translated-value').find('label').should('not.contain.text', value);

    return TranslatedValue;
  }

  public static hasValue(value: any, type: TranslatedValueTypes): typeof TranslatedValue {
    if (type === TranslatedValueTypes.INPUT) {
      cy.get(TranslatedValue.initialSelector).find('input').should('have.value', value);
    } else if (type === TranslatedValueTypes.TEXTAREA){
      cy.get(TranslatedValue.initialSelector).find('textarea').should('have.value', value);
    }

    return TranslatedValue;
  }

  public static hasNotValue(value: any, type: TranslatedValueTypes): typeof TranslatedValue {
    if (type === TranslatedValueTypes.INPUT) {
      cy.get(TranslatedValue.initialSelector).find('input').should('not.have.value', value);
    } else if (type === TranslatedValueTypes.TEXTAREA){
      cy.get(TranslatedValue.initialSelector).find('textarea').should('not.have.value', value);
    }

    return TranslatedValue;
  }

  public static writeValue(value: string | number | string[]): typeof TranslatedValue {
    cy.get(TranslatedValue.initialSelector).find('input, textarea').clear().type(value.toString());

    return TranslatedValue;
  }

  // todo add methods for selecting language
}
