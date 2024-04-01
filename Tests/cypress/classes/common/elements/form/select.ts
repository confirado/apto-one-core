import { Attributes, ElementInterface } from '../../../interfaces/element-interface';
import Chainable = Cypress.Chainable;

export class Select implements ElementInterface {

  public static getByAttr(selector: string): typeof Select {
    cy.get(`[data-cy="${selector}"]`).as('cypressElem');
    cy.get('@cypressElem').should('exist');

    return Select;
  }

  public static get(selector: string): typeof Select {
    cy.get(selector).as('cypressElem');
    cy.get('@cypressElem').should('exist');

    return Select;
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
  public static set(elem: Chainable<JQuery<HTMLElement>>): typeof Select {
    elem.as('cypressElem');

    return Select;
  }

  public static hasLabel(label: string): typeof Select {
    cy.get('@cypressElem').find('label').should('contain.text', label);

    return Select;
  }

  public static hasNotLabel(label: string): typeof Select {
    cy.get('@cypressElem').find('label').should('not.contain.text', label);

    return Select;
  }

  public static hasValue(value: any): typeof Select {
    cy.get('@cypressElem').find('md-select md-select-value').find('.md-text').should('contain.text', value);

    return Select;
  }

  public static hasNotValue(value: any): typeof Select {
    cy.get('@cypressElem').find('md-select md-select-value').find('.md-text').should('not.contain.text', value);

    return Select;
  }

  public static hasError(): typeof Select {
    cy.get('@cypressElem').should('have.class', 'md-input-invalid');

    return Select;
  }

  public static hasNotError(): typeof Select {
    cy.get('@cypressElem').should('not.have.class', 'md-input-invalid');

    return Select;
  }

  /**
   * pass null if attribute is not key value
   * .attributes({ 'not.be.visible': null })
   *
   * @param attributes
   */
  public static attributes(attributes: Attributes): typeof Select {
    for(let condition in attributes) {
      if (attributes[condition] !== null) {
        cy.get('@cypressElem').find('md-select').should(condition, attributes[condition]);
      }
      else {
        cy.get('@cypressElem').find('md-select').should(condition);
      }
    }

    return Select;
  }


  // unique methods to select

  /**
   * if selected then .md-text should exist
   */
  public static isSelected(): typeof Select {
    cy.get('@cypressElem').find('md-select md-select-value').should(($span) => {
      expect($span.find('.md-text')).to.have.length(1);
    });

    return Select;
  }

  /**
   * if not selected then element .md-text should not exist
   */
  public static isNotSelected(): typeof Select {
    cy.get('@cypressElem').find('md-select md-select-value').should(($span) => {
      expect($span.find('.md-text')).to.have.length(0);
    });

    return Select;
  }

  /**
   * selects the given value from select-box
   *
   * @param value
   */
  public static select(value: string): typeof Select {
    cy.get('@cypressElem').click();
    cy.get('.md-select-menu-container.md-active.md-clickable').should('exist');

    cy.get('.md-select-menu-container.md-active.md-clickable').within(() => {
      cy.get('md-content md-option').each($option => {
        cy.wrap($option).find('.md-text').each($mdText => {
          const cellValue = $mdText.text().trim();

          if (cellValue.includes(value)) {
            cy.wrap($option).click();
          }
        });
      })
    });

    return Select;
  }
}
