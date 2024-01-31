/**
 * When applied to a class method it returns some extra methods from the class joined with the origin methods data
 *
 * Example: @AddPropertiesToReturnValue(['endpoint', 'method', 'type'])
 *
 * @param properties
 * @constructor
 */
export function AddPropertiesToReturnValue(properties: string[]) {
  return function (target: any, key: string, descriptor: PropertyDescriptor) {
    const originalMethod = descriptor.value || descriptor.get;

    if (!originalMethod) {
      throw new Error('Unsupported method type. Please ensure that the method is a function or getter.');
    }

    if (descriptor.value) {
      descriptor.value = function (...args: any[]) {
        const originalResult = originalMethod.apply(this, args);

        const extraProperties = properties.reduce((acc, propName) => {
          acc[propName] = target[propName];
          return acc;
        }, {});

        return {
          ...originalResult,
          ...extraProperties,
        };
      };
    } else if (descriptor.get) {
      descriptor.get = function () {
        const originalResult = originalMethod.call(this);

        const extraProperties = properties.reduce((acc, propName) => {
          acc[propName] = target[propName];
          return acc;
        }, {});

        return {
          ...originalResult,
          ...extraProperties,
        };
      };
    }

    Object.defineProperty(target, key, descriptor);
  };
}

/**
 * When calling static getters of the class, this decorator adds some additional properties from class that we give as argument
 *
 * example of usage: @AddPropertiesToReturnValueForClass(['endpoint', 'method', 'type'])
 *
 * this helps us to save our time and not write in each method some static data
 *
 * this is working currently only for static getter methods of the class. if you want to read also non-static getters,
 * then read the data from "target.prototype": target.prototype[key] = methodDecorator(dataProperty);
 *
 * @param propertiesToInclude
 * @constructor
 */
export function AddPropertiesToReturnValueForClass(propertiesToInclude: string[]) {
  return function (target: any, ...args: any[]) {
    const methodDecorator = (originalMethod: Function | undefined) => {
      return function (...methodArgs: any[]) {
        const originalResult = originalMethod.apply(this, methodArgs);

        // Add specified properties to the result
        const additionalProperties: Record<string, any> = {};
        propertiesToInclude.forEach((prop) => {
          additionalProperties[prop] = target[prop];
        });

        return {
          ...originalResult,
          ...additionalProperties,
        };
      };
    };

    Object.getOwnPropertyNames(target).forEach((key) => {
      const dataProperty = Object.getOwnPropertyDescriptor(target, key)?.value;
      const accessorProperty = Object.getOwnPropertyDescriptor(target, key)?.get;

      // todo this is not good tested as not needed yet!
      if (dataProperty) {
        target.prototype[key] = methodDecorator(dataProperty);
      }

      // if we have a getter property, we define a new getter method with that name (we overwrite the existing)
      if (accessorProperty) {
        Object.defineProperty(target, key, {
          get: methodDecorator(accessorProperty),
        });
      }
    });
  };
}
