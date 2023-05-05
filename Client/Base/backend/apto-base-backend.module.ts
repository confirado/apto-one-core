import { NgModule } from '@angular/core';
import { BackendComponent } from './components/backend/backend.component';

@NgModule({
  declarations: [
    BackendComponent
  ],
  exports: [
    BackendComponent
  ],
  imports: [],
  providers: []
})
export class AptoBaseBackendModule {}
