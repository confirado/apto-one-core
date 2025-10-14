import { Injectable } from '@angular/core';
import { MatSnackBar } from '@angular/material/snack-bar';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { initShop } from '@apto-base-frontend/store/shop/shop.actions';
import { selectConnector } from '@apto-base-frontend/store/shop/shop.selectors';
import { CatalogMessageBusService } from '@apto-catalog-frontend/services/catalog-message-bus.service';
import {
  addGuestConfiguration, addGuestConfigurationSuccess, addOfferConfiguration, addOfferConfigurationSuccess, addToBasket, addToBasketSuccess, fetchPartsList,
  fetchPartsListSuccess, getConfigurationState, getConfigurationStateSuccess, getCurrentRenderImageSuccess, getElementComputableValues, getElementComputableValuesSuccess,
  getRenderImagesSuccess, hideLoadingFlagAction, humanReadableStateLoadSuccess, initConfiguration,
  initConfigurationSuccess, onError, setPrevStep, setPrevStepSuccess, setStep, setStepSuccess, updateConfigurationState
}
  from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ConfigurationRepository } from '@apto-catalog-frontend/store/configuration/configuration.repository';
import { ProductRepository } from '@apto-catalog-frontend/store/product/product.repository';
import { Actions, createEffect, ofType } from '@ngrx/effects';
import { Store } from '@ngrx/store';
import { EMPTY, forkJoin, tap } from 'rxjs';
import { filter, map, switchMap, withLatestFrom } from 'rxjs/operators';
import { selectCurrentUser } from '@apto-base-frontend/store/frontend-user/frontend-user.selectors';
import { selectRuleRepairSettings } from '@apto-catalog-frontend/store/product/product.selectors';
import { loginSuccess, logoutSuccess } from '@apto-base-frontend/store/frontend-user/frontend-user.actions';
import { DialogService } from '@apto-catalog-frontend/components/common/dialogs/dialog-service';
import { DialogSizesEnum } from '@apto-frontend/src/configs-static/dialog-sizes-enum';
import { ConfirmationDialogComponent } from '@apto-catalog-frontend-confirmation-dialog';
import { DialogTypesEnum } from '@apto-frontend/src/configs-static/dialog-types-enum';
import { selectContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippets.selectors';
import { ContentSnippet } from '@apto-base-frontend/store/content-snippets/content-snippet.model';
import { MessageBusResponseMessage } from '@apto-base-core/models/message-bus-response';
import { translate } from '@apto-base-core/store/translated-value/translated-value.model';
import { environment } from '@apto-frontend/src/environments/environment';
import { selectConfiguration, selectCurrentPerspective, selectProduct, selectProgressState } from './configuration.selectors';
import {
  Configuration,
  ConfigurationState,
  CurrentSection,
  GetConfigurationResult,
  SectionState
} from './configuration.model';

@Injectable()
export class ConfigurationEffects {
	public constructor(
		private actions$: Actions,
		private configurationRepository: ConfigurationRepository,
		private productRepository: ProductRepository,
		private store$: Store,
		private catalogMessageBusService: CatalogMessageBusService,
		private matSnackBar: MatSnackBar,
    private dialogService: DialogService
	) {}

	public initConfiguration$ = createEffect(() =>
		this.actions$.pipe(
			ofType(initConfiguration),
			withLatestFrom(
				this.store$.select(selectConnector),
				this.store$.select(selectCurrentPerspective),
				this.store$.select(selectConfiguration),
        this.store$.select(selectCurrentUser)
			),
			switchMap(([action, connector, currentPerspective, c, currentUser]) =>
				this.productRepository.findConfigurableProduct(action.payload.id, action.payload.type).pipe(
					switchMap((product) => {
						if (c.productId === product.product.id) {
							return EMPTY;
						}

						const initConfigurationState = {
							productId: product.product.id,
							compressedState: [],
							updates: {
								init: true,
							},
						};

            if (product.configuration) {
              initConfigurationState.updates.init = false;
              initConfigurationState.compressedState = product.configuration.state;
            }

						return this.configurationRepository.getConfigurationState(initConfigurationState).pipe(
							map((configuration) => ({
								connector,
								product,
								configuration: configuration.state as Configuration,
                renderImages: configuration.renderImages,
								currentPerspective,
                currentUser,
                action
							}))
						);
					})
				)
			),
			switchMap((result) =>
				forkJoin([
					this.configurationRepository.getComputedValues(result.product.product.id, result.configuration.compressedState),
					this.configurationRepository.getPerspectives(result.product.product.id, result.configuration.compressedState),
					this.configurationRepository.getStatePrice(result.product.product.id, result.configuration.compressedState, result.connector, result.currentUser),
				]).pipe(
					map((joinResult) => ({
						...result,
						computedValues: joinResult[0],
						perspectives: joinResult[1],
						currentPerspective: this.getCurrentPerspective(joinResult[1], result.currentPerspective),
						statePrice: joinResult[2],
					}))
				)
			),
			map((result) => {
        let sections: SectionState[] = [];
        if (result.product.product.keepSectionOrder === false && result.action.payload.type === null) {
          sections = result.configuration.sections.filter((section) => !section.disabled && !section.hidden);
        } else {
          sections = result.configuration.sections.filter((section) => !section.disabled && !section.hidden && !section.active);
        }
        const currentStep: CurrentSection | null = sections.length > 0 ? { id: sections[0].id, repetition: sections[0].repetition } : null;

				return initConfigurationSuccess({
					payload: {
						connector: result.connector,
						product: result.product.product,
						groups: result.product.groups,
						sections: result.product.sections,
						elements: result.product.elements,
						currentStep,
						productId: result.product.product.id,
						configuration: result.configuration,
						computedValues: result.computedValues,
						perspectives: result.perspectives,
						currentPerspective: result.currentPerspective,
						statePrice: result.statePrice,
            renderImages: result.renderImages,
					},
				});
			})
		)
	);

	public updateConfiguration$ = createEffect(() =>
		this.actions$.pipe(
			ofType(updateConfigurationState),
			withLatestFrom(
        this.store$.select(selectConfiguration),
        this.store$.select(selectCurrentUser),
        this.store$.select(selectConnector)
      ),
			map(([action, store, currentUser, connector]) => getConfigurationState({
					payload: {
						productId: store.productId,
						compressedState: store.state.compressedState,
						connector,
						updates: action.updates,
						currentPerspective: store.currentPerspective,
            currentUser,
					},
				}))
		)
	);

  public getConfigurationState$ = createEffect(() =>
    this.actions$.pipe(
      ofType(getConfigurationState),
      withLatestFrom(
        this.store$.select(selectRuleRepairSettings)
      ),
      switchMap(([action, ruleRepairSettings]) => {
        const payload = {
          ...action.payload,
          updates: {
            ...action.payload.updates,
          },
        };

        if (ruleRepairSettings !== null) {
          payload.updates.repair = ruleRepairSettings;
        }

        return this.configurationRepository.getConfigurationState(payload).pipe(
          filter((result): result is GetConfigurationResult => result !== null),
          map((result) => ({
            connector: action.payload.connector,
            productId: action.payload.productId,
            configuration: result.state,
            renderImages: result.renderImages,
            currentPerspective: action.payload.currentPerspective,
            currentUser: action.payload.currentUser,
            updates: result.updates
          }))
        );
      }),
      switchMap((result) =>
        forkJoin([
          this.configurationRepository.getComputedValues(result.productId, result.configuration.compressedState),
          this.configurationRepository.getPerspectives(result.productId, result.configuration.compressedState),
          this.configurationRepository.getStatePrice(result.productId, result.configuration.compressedState, result.connector, result.currentUser),
        ]).pipe(
          map((joinResult) => ({
            productId: result.productId,
            configuration: result.configuration,
            renderImages: result.renderImages,
            computedValues: joinResult[0],
            perspectives: joinResult[1],
            currentPerspective: this.getCurrentPerspective(joinResult[1], result.currentPerspective),
            statePrice: joinResult[2],
            updates: result.updates
          }))
        )
      ),
      map((state) =>
        getConfigurationStateSuccess({
          payload: {
            productId: state.productId,
            configuration: state.configuration,
            renderImages: state.renderImages,
            computedValues: state.computedValues,
            perspectives: state.perspectives,
            currentPerspective: state.currentPerspective,
            statePrice: state.statePrice,
            updates: state.updates
          },
        })
      )
    )
  );

  public loginStatusChanged$ = createEffect(() =>
    this.actions$.pipe(
      ofType(loginSuccess, logoutSuccess),
      withLatestFrom(
        this.store$.select(selectConfiguration),
        this.store$.select(selectCurrentUser),
        this.store$.select(selectConnector)
      ),
      map(([action, store, currentUser, connector]) =>
        getConfigurationState({
          payload: {
            productId: store.productId,
            compressedState: store.state.compressedState,
            connector,
            updates: {},
            currentPerspective: store.currentPerspective,
            currentUser,
          },
        })
      )
    )
  );

	public addGuestConfiguration$ = createEffect(() =>
		this.actions$.pipe(
			ofType(addGuestConfiguration),
			withLatestFrom(this.store$.select(selectConfiguration)),
			switchMap(([action, store]) =>
				this.configurationRepository.addGuestConfiguration({
					productId: store.productId as string,
					compressedState: store.state.compressedState,
					email: action.payload.email,
					name: action.payload.name,
					sendMail: true,
					id: action.payload.id,
					payload: {},
				})
			),
			map(() => addGuestConfigurationSuccess())
		)
	);

	public addGuestConfigurationSuccess$ = createEffect(
		() =>
			this.actions$.pipe(
				ofType(addGuestConfigurationSuccess),
				map(() => {
					this.matSnackBar.open(
						'Ihre Konfiguration wurde gespeichert. Sie erhalten in Kürze eine E-Mail mit dem Link zur Konfiguration.',
						undefined,
						{ duration: 3000 }
					);
				})
			),
		{ dispatch: false }
	);

  public addOfferConfiguration$ = createEffect(() =>
    this.actions$.pipe(
      ofType(addOfferConfiguration),
      withLatestFrom(this.store$.select(selectConfiguration)),
      switchMap(([action, store]) =>
        this.configurationRepository.addOfferConfiguration({
          productId: store.productId,
          compressedState: store.state.compressedState,
          email: action.payload.email,
          name: action.payload.name,
          payload: action.payload.payload,
        })
      ),
      map(() => addOfferConfigurationSuccess())
    )
  );

  public addOfferConfigurationSuccess$ = createEffect(
    () =>
      this.actions$.pipe(
        ofType(addOfferConfigurationSuccess),
        map(() => {
          this.matSnackBar.open(
            'Ihre Konfiguration wurde gespeichert. Sie erhalten in Kürze eine E-Mail mit dem Link zur Konfiguration.',
            undefined,
            { duration: 3000 }
          );
        })
      ),
    { dispatch: false }
  );

	public onError$ = createEffect(
		() =>
			this.actions$.pipe(
				ofType(onError),
				withLatestFrom(
          this.store$.select(selectLocale).pipe(map((l) => l || environment.defaultLocale)),
          this.store$.select(selectContentSnippet('aptoRules.defaultErrorMessage')),
          this.store$.select(selectContentSnippet('auth.errors'))
        ),
				map(([{ message }, locale, defaultErrorMessage, errors]:
               [{message: MessageBusResponseMessage}, string, ContentSnippet, ContentSnippet]
        ) => {
          let defaultMessage: string = translate(defaultErrorMessage.content, locale);
          let showDefault = true;
          let finalMessage = '';

          // case rule errors
          if (Array.isArray(message.errorPayload)) {
            for (const singleErrorPayload of message.errorPayload) {
              const errorMessage = translate(singleErrorPayload.errorMessage, locale);
              if (errorMessage) {
                showDefault = false;
                finalMessage += `${errorMessage} <br />`;
              }
            }
          }

          // case validation errors
          if (message.errorPayload && message.errorPayload.errorMessage) {
            const errorMessage = translate(message.errorPayload.errorMessage, locale);
            if (errorMessage) {
              showDefault = false;
              finalMessage += `${errorMessage} <br />`;
            }
          }

          if (showDefault) {
            const errorTranslation = errors.children.find((c) => c.name === message.errorType);
            defaultMessage = errorTranslation ? translate(errorTranslation.content, locale) : defaultMessage;
          }

          this.dialogService.openCustomDialog(ConfirmationDialogComponent, DialogSizesEnum.md, {
            type: DialogTypesEnum.ERROR,
            hideIcon: true,
            descriptionText: showDefault ? defaultMessage : finalMessage,
          });
				})
			),
		{ dispatch: false }
	);

	public getHumanReadableState$ = createEffect(() =>
		this.actions$.pipe(
			ofType(initConfigurationSuccess, getConfigurationStateSuccess),
			switchMap((result) =>
				this.catalogMessageBusService.findHumanReadableState(result.payload.productId, result.payload.configuration.compressedState)
			),
			map((payload) => humanReadableStateLoadSuccess({ payload }))
		)
	);

	public getRenderImages$ = createEffect(() =>
		this.actions$.pipe(
			ofType(getCurrentRenderImageSuccess),
			switchMap((action) =>
				this.configurationRepository
					.getRenderImages(action.payload.productId, action.payload.perspectives, action.payload.compressedState)
					.pipe(
						map((renderImagesResult) => ({
							...action,
							renderImages: renderImagesResult,
						}))
					)
			),
			map((state) => getRenderImagesSuccess({ payload: state }))
		)
	);

  public getElementComputableValues$ = createEffect(() =>
    this.actions$.pipe(
      ofType(getElementComputableValues),
      switchMap((action) =>
        this.catalogMessageBusService
          .findElementComputableValues(action.payload.compressedState, action.payload.sectionId, action.payload.elementId, action.payload.repetition)
          .pipe(
            map((searchMapping) => getElementComputableValuesSuccess({ payload: searchMapping })),
          )
      )
    )
  );

	public setPrevStep$ = createEffect(() =>
		this.actions$.pipe(
			ofType(setPrevStep),
			map(() => setPrevStepSuccess())
		)
	);

	public setStep$ = createEffect(() =>
		this.actions$.pipe(
			ofType(setStep),
			map(() => setStepSuccess())
		)
	);

  /**
   * triggered when we go back in configuration
   */
	public resetSteps$ = createEffect(() =>
		this.actions$.pipe(
			ofType(setStepSuccess, setPrevStepSuccess),
			withLatestFrom(
        this.store$.select(selectConfiguration),
        this.store$.select(selectProgressState),
        this.store$.select(selectProduct)
      ),
			map(([action, configuration, progressState, product]) => {
        const removeList: ConfigurationState[] = [];

        if (product.product.keepSectionOrder) {
          for (const section of configuration.state.sections) {
            if (section.active && progressState.afterSteps.some((s) => s.section.id === section.id && s.section.repetition === section.repetition)) {
              configuration.state.elements
                .filter((element) => element.sectionId === section.id && element.sectionRepetition === section.repetition && element.active)
                .forEach((e) =>
                  removeList.push({
                    sectionId: section.id,
                    elementId: e.id,
                    sectionRepetition: e.sectionRepetition,
                    property: '',
                    value: '',
                  })
                );
            }
          }

          // this should NOT run in case, otherwise it causes issue with add remove section,
          // somehow getConfigurationState call fails
          return updateConfigurationState({
            updates: {
              remove: removeList,
            },
          });
        }

        // todo this is here for just to return something
        return hideLoadingFlagAction();
			})
		)
	);

	public addToBasket$ = createEffect(() =>
		this.actions$.pipe(
			ofType(addToBasket),
			withLatestFrom(
        this.store$.select(selectConfiguration),
        this.store$.select(selectCurrentUser),
        this.store$.select(selectConnector)
      ),
			switchMap(([{ payload }, configurationState, currentUser, connector]) => {
				if (!configurationState.productId) {
					return EMPTY;
				}

				let additionalData: any = {
          productImages: payload.productImage ? [payload.productImage] : [],
        };

        if (payload.type === 'REQUEST_FORM') {
          let customerGroup = connector?.customerGroup;
          if (currentUser) {
            customerGroup = {
              id: currentUser.customerGroup.externalId,
              name: currentUser.customerGroup.name,
              inputGross: currentUser.customerGroup.inputGross,
              showGross: currentUser.customerGroup.showGross,
            };
          }

					additionalData = {
						formData: {
              customer: payload.formData,
              quantity: {
                value: {
                  name: `${configurationState.quantity} Stück`,
                  value: configurationState.quantity,
                },
              },
            },
						humanReadableState: payload.humanReadableState,
						locale: connector?.locale,
						compressedState: configurationState.state.compressedState,
						shopCurrency: connector?.shopCurrency,
						displayCurrency: connector?.displayCurrency,
						customerGroup,
            customerGroupExternalId: customerGroup.id,
            productId: configurationState.productId,
					};
				}

        // this is the case when user from the basket clicks "Konfiguration bearbeiten" and then changes the config and adds again to the basket
        if (payload.configurationId && payload.configurationType === 'basket') {
          return this.configurationRepository.updateBasket({
            productId: configurationState.productId,
            configurationId: payload.configurationId,
            locale: connector?.locale,
            compressedState: configurationState.state.compressedState,
            quantity: configurationState.quantity,
            perspectives: configurationState.perspectives,
            sessionCookies: connector?.sessionCookies,
            additionalData,
          });
        }
          return this.configurationRepository.addToBasket({
            productId: configurationState.productId,
            locale: connector?.locale,
            compressedState: configurationState.state.compressedState,
            quantity: configurationState.quantity,
            perspectives: configurationState.perspectives,
            sessionCookies: connector?.sessionCookies,
            additionalData,
          });
			}),
      switchMap((result) => [
        addToBasketSuccess(),
        initShop(),
      ])
		)
	);

  public fetchPartsList$ = createEffect(() =>
    this.actions$.pipe(
      ofType(fetchPartsList),
      withLatestFrom(
        this.store$.select(selectConfiguration),
        this.store$.select(selectConnector)
      ),
      switchMap(([action, state, connector]) =>
        this.configurationRepository.fetchPartsList({
          productId: state.productId as string,
          compressedState: state.state.compressedState,
          currency: connector?.displayCurrency.currency,
          customerGroupExternalId: connector?.customerGroup.id,
        })
      ),
      map((result) => fetchPartsListSuccess({ payload: result }))
    )
  );

	public getCurrentPerspective(perspectives: string[], currentPerspective: string | null): string | null {
		if (perspectives.length === 0) {
			return null;
		}

		if (this.hasRenderImagesPerspective(perspectives, currentPerspective)) {
			return currentPerspective;
		}

		return perspectives[0];
	}

	public hasRenderImagesPerspective(perspectives: string[], currentPerspective: string | null): boolean {
		let result = false;

		perspectives.every((perspective) => {
			if (perspective === currentPerspective) {
				result = true;
				return false;
			}
			return true;
		});

		return result;
	}
}
