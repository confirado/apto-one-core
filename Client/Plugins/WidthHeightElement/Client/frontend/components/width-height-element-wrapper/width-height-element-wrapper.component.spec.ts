import { ComponentFixture, TestBed } from '@angular/core/testing';

import { WidthHeightElementWrapperComponent } from './width-height-element-wrapper.component';

describe('WidthHeightElementWrapperComponent', () => {
  let component: WidthHeightElementWrapperComponent;
  let fixture: ComponentFixture<WidthHeightElementWrapperComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ WidthHeightElementWrapperComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(WidthHeightElementWrapperComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
