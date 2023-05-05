import { Route, Routes } from "@angular/router";

export type RegisteredRoute = {
  route: Route;
  priority: number;
}

export class RouterRegistry {
  private static routes: Array<RegisteredRoute> = [];
  private constructor() {}

  public static registerRoutes(routes: RegisteredRoute[]) {
    for (let i = 0; i < routes.length; i++) {
      RouterRegistry.registerRoute(routes[i].route, routes[i].priority);
    }
  }

  public static registerRoute(route: Route, priority: number) {
    let routeIndex = null;

    // find existing component
    for (let i = 0; i < RouterRegistry.routes.length; i++) {
      if (RouterRegistry.routes[i].route.path === route.path) {
        routeIndex = i;
        break;
      }
    }

    // create or update component
    if (routeIndex === null) {
      // if component not exist create new
      RouterRegistry.routes.push({
        route: route,
        priority: priority
      });
    } else if(RouterRegistry.routes[routeIndex].priority <= priority) {
      // if component not exist create new
      RouterRegistry.routes[routeIndex].route = route;
      RouterRegistry.routes[routeIndex].priority = priority;
    }
  }

  static getRoutes() {
    let routes: Routes = [];
    for (let i = 0; i < RouterRegistry.routes.length; i++) {
      routes.push(RouterRegistry.routes[i].route);
    }
    return routes;
  }
}
