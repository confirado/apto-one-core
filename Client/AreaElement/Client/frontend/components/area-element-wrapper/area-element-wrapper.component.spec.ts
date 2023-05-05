import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AreaElementWrapperComponent } from './area-element-wrapper.component';

describe('AreaElementWrapperComponent', () => {
  let component: AreaElementWrapperComponent;
  let fixture: ComponentFixture<AreaElementWrapperComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ AreaElementWrapperComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AreaElementWrapperComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
