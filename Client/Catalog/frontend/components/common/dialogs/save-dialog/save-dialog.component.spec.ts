import { ComponentFixture, TestBed } from '@angular/core/testing';
import { MatLegacyDialogRef as MatDialogRef } from '@angular/material/legacy-dialog';
import { Store } from '@ngrx/store';
import { of } from 'rxjs';

import { addGuestConfiguration } from '@apto-catalog-frontend-configuration-actions';
import { SaveDialogComponent } from '@apto-catalog-frontend-save-dialog';

describe('SaveDialogComponent', () => {
  let component: SaveDialogComponent;
  let fixture: ComponentFixture<SaveDialogComponent>;
  let dialogRef: jasmine.SpyObj<MatDialogRef<SaveDialogComponent>>;
  let store: jasmine.SpyObj<Store>;

  beforeEach(async () => {
    dialogRef = jasmine.createSpyObj<MatDialogRef<SaveDialogComponent>>('MatDialogRef', ['close']);
    store = jasmine.createSpyObj<Store>('Store', ['dispatch', 'select']);
    store.select.and.returnValue(of(null));

    await TestBed.configureTestingModule({
      declarations: [SaveDialogComponent],
      providers: [
        { provide: MatDialogRef, useValue: dialogRef },
        { provide: Store, useValue: store },
      ],
    })
      .overrideComponent(SaveDialogComponent, { set: { template: '' } })
    .compileComponents();

    fixture = TestBed.createComponent(SaveDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('dispatches the guest configuration and closes the dialog on submit', () => {
    component.formGroup.setValue({ email: 'user@example.com', name: 'User', id: 'configuration-1' });

    component.onSubmit();

    expect(store.dispatch).toHaveBeenCalledWith(addGuestConfiguration({
      payload: { email: 'user@example.com', name: 'User', id: 'configuration-1' },
    }));
    expect(dialogRef.close).toHaveBeenCalled();
  });
});
