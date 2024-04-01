import { Attributes, ElementInterface } from '../../../interfaces/element-interface';
import Chainable = Cypress.Chainable;

export class Checkbox implements ElementInterface {

  public static getByAttr(selector: string): typeof Checkbox {
    cy.get(`[data-cy="${selector}"]`).as('checkboxElem');
    cy.get('@checkboxElem').should('exist');

    return Checkbox;
  }

  public static get(selector: string): typeof Checkbox {
    cy.get(selector).as('checkboxElem');
    cy.get('@checkboxElem').should('exist');

    return Checkbox;
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
  public static set(elem: Chainable<JQuery<HTMLElement>>): typeof Checkbox {
    elem.as('checkboxElem');

    return Checkbox;
  }

  public static hasLabel(label: string): typeof Checkbox {
    cy.get('@checkboxElem').find('.md-label').should('contain.text', label);

    return Checkbox;
  }

  public static hasNotLabel(label: string): typeof Checkbox {
    cy.get('@checkboxElem').find('.md-label').should('not.contain.text', label);

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
        cy.get('@checkboxElem').should(condition, attributes[condition]);
      }
      else {
        cy.get('@checkboxElem').should(condition);
      }
    }

    return Checkbox;
  }


  // unique to checkbox methods

  public static isChecked(): typeof Checkbox {
    cy.get('@checkboxElem').find('md-checkbox').should('have.class', 'md-checked');

    return Checkbox;
  }

  public static isUnChecked(): typeof Checkbox {
    cy.get('@checkboxElem').find('md-checkbox').should('not.have.class', 'md-checked');

    return Checkbox;
  }

  public static check(): typeof Checkbox {
    cy.get('@checkboxElem').find('md-checkbox').then(($checkbox) => {
      if (!$checkbox.hasClass('md-checked')) {
        cy.wrap($checkbox).click();

        Checkbox.isChecked();
      }
    });

    return Checkbox;
  }

  public static unCheck(): typeof Checkbox {
    cy.get('@checkboxElem').find('md-checkbox').then(($checkbox) => {
      if ($checkbox.hasClass('md-checked')) {
        cy.wrap($checkbox).click();

        Checkbox.isUnChecked();
      }
    });

    return Checkbox;
  }

  public static click(): typeof Checkbox {
    cy.get('@checkboxElem').find('md-checkbox').click();

    return Checkbox;
  }
}
