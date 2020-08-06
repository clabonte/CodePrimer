# Business Bundle - DataClient Component
![Business Bundle - DataClient](../images/BusinessBundle-DataClient.png)

A DataClient is the component in a [Business Bundle](Overview.md) responsible to interface with an underlying storage engine (e.g. database, cache, filesystem), to persist Business Model objects based on the internal storage, pattern and framework selected (e.g. through the manipulation of entities).

## Guidelines
In order to build a **production-grade solution**, the following guidelines should be applied to DataClient components design and implementation:

 - A `ReadDataClient` interface should be defined to allow Business Model objects to be fetched from the storage engine.
   - This interface provides a **read only** access to the storage engine to **other components within the same Business Bundle** in order to allow them to **prepare an event or message** to trigger/send.
 - A `FullDataClient` interface must be defined as an **extension of the `ReadDataClient`** interface to add persistence (aka write) capabilities to the Business Bundle.
   - This interface should **only be accessed by the [Engine](Engine.md) component** to ensure business-level consistency and data integrity.
   - This interface should expose **persistence methods matching the output of the business processes rather than _generic create and update_** methods typically available in CRUD-based solutions. The rationale being that we are operating at the business level, not to trying to reinvent ORM patterns.
   - **Note**: the actual implementation is free to use the pattern of choice based on the persistence solution selected...  
 - **Automated tests must be designed against the DataClient interfaces** and executed against the concrete classes
   - It ensures a concrete class is fully compatible with the business contract defined by the interface
   - It ensures the concrete class is truly interchangeable from a business point of view.
 - A DataClient component must provide a **health check** method that can be **called by the [Engine](Engine.md)**'s own health check to ensure this component is working as expected in production in order to quickly **detect failures with the underlying storage engine**. 
    - This allows any issue that may impact the business to be quickly identified and reported for resolution.
