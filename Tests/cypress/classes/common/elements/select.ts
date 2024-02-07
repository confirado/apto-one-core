import { Attributes, Element } from './element.interface';

export class Select implements Element {
  private static initialSelector: string;

  public static getByAttr(selector: string): typeof Select {
    Select.initialSelector = `[data-cy="${selector}"]`;
    cy.get(Select.initialSelector).should('exist');

    return Select;
  }

  public static get(selector: string): typeof Select {
    Select.initialSelector = selector;
    cy.get(Select.initialSelector).should('exist');

    return Select;
  }

  public static hasLabel(label: string): typeof Select {
    cy.get(Select.initialSelector).find('label').should('contain.text', label);

    return Select;
  }

  public static hasNotLabel(label: string): typeof Select {
    cy.get(Select.initialSelector).find('label').should('not.contain.text', label);

    return Select;
  }

  public static hasValue(value: any): typeof Select {
    cy.get(Select.initialSelector).find('md-select md-select-value').find('.md-text').should('contain.text', value);

    return Select;
  }

  public static hasNotValue(value: any): typeof Select {
    cy.get(Select.initialSelector).find('md-select md-select-value').find('.md-text').should('not.contain.text', value);

    return Select;
  }

  public static hasError(): typeof Select {
    cy.get(Select.initialSelector).should('have.class', 'md-input-invalid');

    return Select;
  }

  public static hasNotError(): typeof Select {
    cy.get(Select.initialSelector).should('not.have.class', 'md-input-invalid');

    return Select;
  }

  public static attributes(attributes: Attributes): typeof Select {
    for(let condition in attributes) {
      if (attributes[condition] !== null) {
        cy.get(Select.initialSelector).find('md-select').should(condition, attributes[condition]);
      }
      else {
        cy.get(Select.initialSelector).find('md-select').should(condition);
      }
    }

    return Select;
  }


  // unique methods to select

  /**
   * if selected then .md-text should exist
   */
  public static isSelected() {
    cy.get(Select.initialSelector).find('md-select md-select-value').should(($span) => {
      expect($span.find('.md-text')).to.have.length(1);
    });

    return Select;
  }

  /**
   * if not selected then element .md-text should not exist
   */
  public static isNotSelected() {
    cy.get(Select.initialSelector).find('md-select md-select-value').should(($span) => {
      expect($span.find('.md-text')).to.have.length(0);
    });

    return Select;
  }
}
