import { Interception } from 'cypress/types/net-stubbing';
import { RequestHandler } from '../../classes/requestHandler';
import { Rules } from '../../classes/pages/rules/rules';
import { Product } from '../../classes/pages/product/product';
import { Login } from '../../classes/pages/login/login';
import { Table } from '../../classes/common/elements/table';
import { TableActionTypes } from '../../classes/enums/table-action-types';
import { Tabs } from '../../classes/common/elements/tabs';
import { Input } from '../../classes/common/elements/form/input';
import { Core } from '../../classes/common/core';
import { Checkbox } from '../../classes/common/elements/form/checkbox';
import { Select } from '../../classes/common/elements/form/select';
import { TranslatedValue, TranslatedValueTypes } from '../../classes/common/elements/custom/translated-value';
import { Textarea } from '../../classes/common/elements/form/textarea';

describe('Product list', () => {

  const baseUrl = Cypress.env('baseUrl');
  var ruleName = '';

  beforeEach(() => {

    /*  Yes!, we have to do this in every test as cypress removes all the information about page in each test, after each test we get a
    blank page pointing nowhere
    Check this:
    https://docs.cypress.io/guides/core-concepts/test-isolation#Test-Isolation-Disabled   */
    Login.login()
      .then((data) => {
        RequestHandler.registerInterceptions(Product.initialRequests);
        Product.visit(true);
      });
  });

  before(() => {
    ruleName = Rules.generateName();
  });

  it('Checks create rule is working', () => {

    Rules.isCorrectPage();

    cy.wait(RequestHandler.getAliasesFromRequests(Product.initialRequests)).then(($responses: Interception[]) => {

      Core.checkResponsesForError($responses);

      RequestHandler.registerInterceptions(Product.editProductQueryList);

      Table.getByAttr('product-list')
        .action(TableActionTypes.EDIT, 'Fenster Step by Step');

      cy.wait(RequestHandler.getAliasesFromRequests(Product.editProductQueryList)).then(() => {

        // lets create a rule
        Tabs.get('md-tabs')
          .hasContent()
          .select('Regeln');

        Input.getByAttr('rule-name')
          .writeValue(ruleName);

        RequestHandler.registerInterceptions(Rules.createRuleRequests);

        cy.dataCy('rule-insert-button').click();

        cy.wait(RequestHandler.getAliasesFromRequests(Rules.createRuleRequests)).then(() => {
          Core.checkResponsesForError($responses);

          // check that rule was really created
          Table.getByAttr('rules-table')
            .hasValue(ruleName);
        });
      });
    });
  });

  it.only('Checks edit rule page has is working and has correct inputs', () => {

    Rules.isCorrectPage();

    ruleName = 'rules-6okR7n14';

    cy.wait(RequestHandler.getAliasesFromRequests(Product.initialRequests)).then(($responses: Interception[]) => {
      Core.checkResponsesForError($responses);

      RequestHandler.registerInterceptions(Product.editProductQueryList);

      Table.getByAttr('product-list').action(TableActionTypes.EDIT, 'Fenster Step by Step');

      cy.wait(RequestHandler.getAliasesFromRequests(Product.editProductQueryList)).then(() => {

        Tabs.get('md-tabs')
          .hasContent()
          .select('Regeln');

        RequestHandler.registerInterceptions(Rules.editRuleRequests);

        Table.getByAttr('rules-table').action(TableActionTypes.EDIT, ruleName);

        cy.wait(RequestHandler.getAliasesFromRequests(Rules.editRuleRequests)).then(() => {
          Core.checkResponsesForError($responses);


          // check "Regel" tab
          Checkbox.getByAttr('rule-active')
            .isUnChecked()
            .hasLabel('Regel aktiv');

          Checkbox.getByAttr('rule-soft-rule')
            .isUnChecked()
            .hasLabel('Weiche Regel');

          Input.getByAttr('rule-position')
            .hasLabel('Position:')
            .hasValue(0);

          Select.getByAttr('rule-conditions-operator')
            .hasLabel('Verknüpfung Bedingung:')
            .hasValue('UND');

          Select.getByAttr('rule-implications-operator')
            .hasLabel('Verknüpfung Auswirkung:')
            .hasValue('UND');

          TranslatedValue.getByAttr('rule-error-message')
            .hasLabel('Fehlermeldung:')
            .hasValue('', TranslatedValueTypes.TEXTAREA);

          Textarea.getByAttr('rule-description')
            .hasLabel('Beschreibung:')
            .hasValue('');


          // Check "Bedingung" tab
          Tabs.getByAttr('rule-edit-dialog')
            .hasContent()
            .select('Bedingung');

          // wait until tab animation is done
          cy.dataCy('rule-edit-dialog')
            .find('md-tabs-content-wrapper md-tab-content:nth-child(2)')
            .should('be.visible').then(() => {

              Select.getByAttr('condition-criterion-type')
                .hasLabel('Type:')
                .hasValue('Standard');

              Select.getByAttr('condition-section')
                .hasLabel('Sektion:')
                .isNotSelected();

              Select.getByAttr('condition-element')
                .hasLabel('Element:')
                .attributes({ 'have.attr': 'disabled' })
                .isNotSelected();

              Select.getByAttr('condition-properties')
                .hasLabel('Feld:')
                .attributes({ 'have.attr': 'disabled' })
                .isNotSelected();

              Select.getByAttr('condition-computed-value')
                .hasLabel('Berechneter Wert:')
                .attributes({ 'not.be.visible': null })
                .isNotSelected();

              Select.getByAttr('condition-operator')
                .hasLabel('Operator:')
                .isNotSelected();

              Input.getByAttr('condition-value')
                .hasLabel('Wert:')
                .attributes({ 'have.attr': 'disabled' })
                .hasValue('');
          });


          // Check "Auswirkung" tab
          Tabs.getByAttr('rule-edit-dialog')
            .hasContent()
            .select('Auswirkung')
            .select('Auswirkung'); // for some reason first click is not working

          // wait until tab animation is done
          cy.dataCy('rule-edit-dialog')
            .find('md-tabs-content-wrapper md-tab-content:nth-child(3)')
            .should('be.visible').then(() => {

              Select.getByAttr('implication-criterion-type')
                .hasLabel('Type:')
                .hasValue('Standard');

              Select.getByAttr('implication-section')
                .hasLabel('Sektion:')
                .isNotSelected();

              Select.getByAttr('implication-element')
                .hasLabel('Element:')
                .attributes({ 'have.attr': 'disabled' })
                .isNotSelected();

              Select.getByAttr('implication-properties')
                .hasLabel('Feld:')
                .attributes({ 'have.attr': 'disabled' })
                .isNotSelected();

              Select.getByAttr('implication-computed-value')
                .hasLabel('Berechneter Wert:')
                .attributes({ 'not.be.visible': null })
                .isNotSelected();

              Select.getByAttr('implication-operator')
                .hasLabel('Operator:')
                .isNotSelected();

              Input.getByAttr('implication-value')
                .hasLabel('Wert:')
                .attributes({ 'have.attr': 'disabled' })
                .hasValue('');
          });
        });
      });
    });
  });
});
