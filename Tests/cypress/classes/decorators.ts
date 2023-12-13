// decorators.ts
export function AddPropertiesToReturnValue(target: any, key: string, descriptor: PropertyDescriptor) {
  const originalMethod = descriptor.value || descriptor.get;

  if (!originalMethod) {
    throw new Error('Unsupported method type. Please ensure that the method is a function or getter.');
  }

  if (descriptor.value) {
    descriptor.value = function (...args: any[]) {
      // Call the original method
      const originalResult = originalMethod.apply(this, args);

      // Add extra properties to the result
      return {
        ...originalResult,
        endpoint: target.endpoint,
        method: target.method,
        type: target.type,
      };
    };
  } else if (descriptor.get) {
    descriptor.get = function () {
      // Call the original getter
      const originalResult = originalMethod.call(this);

      // Add extra properties to the result
      return {
        ...originalResult,
        endpoint: target.endpoint,
        method: target.method,
        type: target.type,
      };
    };
  }

  Object.defineProperty(target, key, descriptor);
}




// decorators.ts
export function AddPropertiesToReturnValueForClass1(target: any) {
  const methodDecorator = (originalMethod: Function | undefined) => {
    if (!originalMethod) {
      throw new Error('Unsupported method type. Please ensure that the method is a function or getter.');
    }

    return function (...args: any[]) {
      // Call the original method
      const originalResult = originalMethod.apply(this, args);

      // Add extra properties to the result
      return {
        ...originalResult,
        endpoint: target.endpoint,
        method: target.method,
        type: target.type,
      };
    };
  };

  // Get all method and property names of the class
  const keys = Object.getOwnPropertyNames(target)
    .concat(Object.getOwnPropertyNames(target.prototype));

  console.error('target')
  console.dir(target, {depth: null})

  // console.error('keys')
  // console.log(keys)

  console.error('Object.getOwnPropertyNames(target)')
  console.log(Object.getOwnPropertyNames(target))

  console.error('Object.getOwnPropertyNames(target.prototype)')
  console.log(Object.getOwnPropertyNames(target.prototype))

  console.error('Object.getOwnPropertyNames(target.prototype)')
  console.log(Object.getOwnPropertyDescriptors(target))

  console.log(target.endpoint)
  console.log(target.method)
  console.log(target.type)


  // Apply the decorator to each method
  keys.forEach(key => {
    const originalMethod = Object.getOwnPropertyDescriptor(target.prototype, key)?.value;
    const originalGetter = Object.getOwnPropertyDescriptor(target.prototype, key)?.get;

    // console.error('originalMethod')
    // console.log(originalMethod)
    //
    // console.error('originalGetter')
    // console.log(originalGetter)


    if (originalMethod) {
      target.prototype[key] = methodDecorator(originalMethod);
    } else if (originalGetter) {
      Object.defineProperty(target.prototype, key, {
        get: methodDecorator(originalGetter),
      });
    }
  });
}

// decorators.ts
export function AddPropertiesToReturnValueForClass(propertiesToInclude: string[]) {
  const classInternalProperties = ['length', 'name', 'prototype'];

  console.log('arguments')
  console.dir(arguments)

  return function (target: any, ...args: any[]) {
    const methodDecorator = (originalMethod: Function | undefined) => {
      if (!originalMethod) {
        throw new Error('Unsupported method type. Please ensure that the method is a function or getter.');
      }

      return function (...methodArgs: any[]) {
        // Call the original method
        const originalResult = originalMethod.apply(this, methodArgs);

        // Add specified properties to the result
        const additionalProperties: Record<string, any> = {};
        propertiesToInclude.forEach(prop => {
          additionalProperties[prop] = target[prop];
        });

        return {
          ...originalResult,
          ...additionalProperties,
        };
      };
    };


    // Apply the decorator to each method
    Object.getOwnPropertyNames(target).forEach(key => {
      console.error('-------------------------------------------')
      console.log(key)
      console.log(Object.getOwnPropertyDescriptor(target, key))

      const valueProperty = Object.getOwnPropertyDescriptor(target, key)?.value;
      const getProperty = Object.getOwnPropertyDescriptor(target, key)?.get;

      if (classInternalProperties.includes(key)) {
        // return;
      }


      if (valueProperty) {
        console.error('descriptor value')
        console.dir(Object.getOwnPropertyDescriptor(target, key))
      }

      if (getProperty && typeof getProperty === 'function') {
        console.error('descriptor get')
        console.dir(Object.getOwnPropertyDescriptor(target, key))
      }



      // console.log('valueProperty')
      // console.log(valueProperty)
      //
      // console.log('getterProperty')
      // console.log(getterProperty)

      if (valueProperty) {
        target.prototype[key] = methodDecorator(valueProperty);
      } else if (getProperty) {
        Object.defineProperty(target.prototype, key, {
          get: methodDecorator(getProperty),
        });
      }
    });
  };
}



