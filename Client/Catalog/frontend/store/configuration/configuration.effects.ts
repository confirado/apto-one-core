import { Injectable } from '@angular/core';
import { MatSnackBar } from '@angular/material/snack-bar';
import { selectLocale } from '@apto-base-frontend/store/language/language.selectors';
import { initShop } from '@apto-base-frontend/store/shop/shop.actions';
import { selectConnector } from '@apto-base-frontend/store/shop/shop.selectors';
import { CatalogMessageBusService } from '@apto-catalog-frontend/services/catalog-message-bus.service';
import {
  addGuestConfiguration,
  addGuestConfigurationSuccess,
  addToBasket, addToBasketSuccess,
  getConfigurationState,
  getConfigurationStateSuccess,
  getCurrentRenderImageSuccess,
  getRenderImagesSuccess,
  humanReadableStateLoadSuccess,
  initConfiguration,
  initConfigurationSuccess,
  onError,
  setPrevStep,
  setPrevStepSuccess,
  setStep,
  setStepSuccess,
  updateConfigurationState,
} from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ConfigurationRepository } from '@apto-catalog-frontend/store/configuration/configuration.repository';
import { ProductRepository } from '@apto-catalog-frontend/store/product/product.repository';
import { Actions, createEffect, ofType } from '@ngrx/effects';
import { Store } from '@ngrx/store';
import { EMPTY, forkJoin } from 'rxjs';
import { filter, map, switchMap, withLatestFrom } from 'rxjs/operators';
import { Element } from '../product/product.model';
import { Configuration, CurrentSection } from './configuration.model';
import { selectConfiguration, selectCurrentPerspective, selectProduct, selectProgressState } from './configuration.selectors';
import { selectCurrentUser } from '@apto-base-frontend/store/frontend-user/frontend-user.selectors';
import { selectRuleRepairSettings } from '@apto-catalog-frontend/store/product/product.selectors';
import { loginSuccess, logoutSuccess } from '@apto-base-frontend/store/frontend-user/frontend-user.actions';

interface GetConfigurationResult {
  state: Configuration,
  renderImages: []
}

@Injectable()
export class ConfigurationEffects {
	public constructor(
		private actions$: Actions,
		private configurationRepository: ConfigurationRepository,
		private productRepository: ProductRepository,
		private store$: Store,
		private catalogMessageBusService: CatalogMessageBusService,
		private matSnackBar: MatSnackBar
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
				const sections = result.configuration.sections.filter((section) => !section.disabled && !section.hidden && !section.active);
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
            renderImages: result.renderImages
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
        this.store$.select(selectCurrentUser)
      ),
			map(([action, store, currentUser]) =>
				getConfigurationState({
					payload: {
						productId: store.productId,
						compressedState: store.state.compressedState,
						connector: store.connector,
						updates: action.updates,
						currentPerspective: store.currentPerspective,
            currentUser: currentUser
					},
				})
			)
		)
	);

  public getConfigurationState$ = createEffect(() =>
    this.actions$.pipe(
      ofType(getConfigurationState),
      withLatestFrom(
        this.store$.select(selectRuleRepairSettings),
      ),
      switchMap(([action, ruleRepairSettings]) => {
        let payload = {
          ...action.payload,
          updates: {
            ...action.payload.updates
          }
        }

        if (null !== ruleRepairSettings) {
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
          })),
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
        this.store$.select(selectCurrentUser)
      ),
      map(([action, store, currentUser]) =>
        getConfigurationState({
          payload: {
            productId: store.productId,
            compressedState: store.state.compressedState,
            connector: store.connector,
            updates: {},
            currentPerspective: store.currentPerspective,
            currentUser: currentUser
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
					id: '',
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

	public onError$ = createEffect(
		() =>
			this.actions$.pipe(
				ofType(onError),
				withLatestFrom(this.store$.select(selectProduct), this.store$.select(selectLocale).pipe(map((l) => l || 'de-DE'))),
				map(([{ message }, product, locale]) => {
					const sectionName = product.sections.find((e) => e.id === message.errorPayload.section)?.name?.[locale];

					const element: Element<any> | undefined = product.elements.find((e) => e.id === message.errorPayload.element);
					const elementName = element?.name?.[locale];

					this.matSnackBar.open(
						`Der Wert ${message.errorPayload.value} ist für das Feld ${message.errorPayload.property} im Element ${elementName} der Sektion ${sectionName} nicht zulässig.`,
						undefined,
						{ duration: 3000 }
					);
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

	public resetSteps$ = createEffect(() =>
		this.actions$.pipe(
			ofType(setStepSuccess, setPrevStepSuccess),
			withLatestFrom(this.store$.select(selectConfiguration), this.store$.select(selectProgressState)),
			map(([action, configuration, progressState]) => {
				const removeList: { sectionId: string; elementId: string; sectionRepetition?: number; property: string; value: string }[] = [];

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

				return updateConfigurationState({
					updates: {
						remove: removeList,
					},
				});
			})
		)
	);

	public addToBasket$ = createEffect(() =>
		this.actions$.pipe(
			ofType(addToBasket),
			withLatestFrom(
        this.store$.select(selectConfiguration),
        this.store$.select(selectCurrentUser)
      ),
			switchMap(([{ payload }, configurationState, currentUser]) => {
				if (!configurationState.productId) {
					return EMPTY;
				}

				let additionalData: any = {
          productImages: payload.productImage ? [payload.productImage] : []
        };

        if (payload.type === 'REQUEST_FORM') {
          let customerGroup = configurationState.connector?.customerGroup;
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
                  name: configurationState.quantity + ' Stück',
                  value: configurationState.quantity
                }
              }
            },
						humanReadableState: payload.humanReadableState,
						locale: configurationState.connector?.locale,
						compressedState: configurationState.state.compressedState,
						shopCurrency: configurationState.connector?.shopCurrency,
						displayCurrency: configurationState.connector?.displayCurrency,
						customerGroup: customerGroup,
            customerGroupExternalId: customerGroup.id,
            productId: configurationState.productId,
					};
				}

				return this.configurationRepository.addToBasket({
					productId: configurationState.productId,
					locale: configurationState.connector?.locale,
					compressedState: configurationState.state.compressedState,
					quantity: configurationState.quantity,
					perspectives: configurationState.perspectives,
					sessionCookies: configurationState.connector?.sessionCookies,
					additionalData
				});
			}),
      switchMap(result => [
        addToBasketSuccess(),
        initShop()
      ])
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
