import Md5 from '@typo3/backend/hashing/md5.js';

export class Event {
  delegationSelectorsMap = {}
  supportedEvents = ['change', 'click', 'keyup']
  nodesHasEventListener = []

  /**
   * @param {Event} event
   */
  listener = (event) => {
    let elementRegisteredNode = event.currentTarget;
    let elementRegisteredNodeHash = this.getHashOfNode(elementRegisteredNode)
    let element = event.target;
    let type = event.type;
    let forceBreak = false;

    const searchForMatches = (event, key) => {
      for (const selector in this.delegationSelectorsMap[key]) {
        if (element.matches(selector)) {
          event.stopPropagation = () => forceBreak = true;

          const callbackList = this.delegationSelectorsMap[key][selector][type];
          if (callbackList) {
            callbackList.forEach((callback) => callback(element, event));
          }
        }
      }
    }

    while (element && element !== document.documentElement) {
      if (this.delegationSelectorsMap[elementRegisteredNodeHash]) {
        searchForMatches(event, elementRegisteredNodeHash);
      } else {
        let objectKeys = Object.keys(this.delegationSelectorsMap);
        objectKeys.forEach(key => {
          searchForMatches(event, key);
        })
      }

      if (forceBreak) break;

      element = element.parentElement;
    }
  }

  /**
   * @param {HTMLElement} node
   * @param {?string} type
   */
  addEvent = (node, type) => {
    if (typeof node === 'undefined') throw new Error(`The provided element is empty!`);
    if (type && type !== '' && !this.supportedEvents.includes(type)) throw new Error(`The provided type "${type}" is currently not supported! Supported EventTypes: ${this.supportedEvents.join(' ')}`);
    if (!this.nodesHasEventListener.includes(node) || !this.nodesHasEventListener[node].includes(type)) {
      if (typeof type !== 'undefined' && type !== '') {
        node.addEventListener(type, this.listener);
      } else {
        this.supportedEvents.forEach(eventType => {
          node.addEventListener(eventType, this.listener)
        });
      }

      if (!this.nodesHasEventListener.includes(node)) {
        this.nodesHasEventListener[node] = [];
      }

      this.nodesHasEventListener[node].push(type);
    }
  }

  /**
   *
   * @param {string} eventTypes A string containing one or more space-separated JavaScript event types, such as "click" or "keydown," or custom event names.
   * @param {string} selector A selector to filter the elements that trigger the event.
   * @param {Function} callback A function to execute at the time the event is triggered.
   * @param {Node} node
   * @returns {Function} Function to remove the delegation
   */
  delegate = (eventTypes, selector, callback, node = document) => {
    if (typeof eventTypes === 'undefined' || eventTypes === '') throw new Error('The provided event is empty.');
    if (typeof selector === 'undefined' || selector === '') throw new Error('The provided selector is empty.');
    if (typeof callback === 'undefined' || typeof callback !== 'function') throw new Error('Specify an callback.');


    let _eventTypes = eventTypes.split(' ');

    _eventTypes.forEach(eventType => {
      if (!this.supportedEvents.includes(eventType)) throw new Error('Event is currently not supported');
      this.addEvent(node, eventType);

      let hashOfNode = this.getHashOfNode(node);

      if (!this.delegationSelectorsMap[hashOfNode] || !this.delegationSelectorsMap[hashOfNode][selector] || !this.delegationSelectorsMap[hashOfNode][selector][eventType]) {
        if (!this.delegationSelectorsMap[hashOfNode]) {
          this.delegationSelectorsMap[hashOfNode] = [];
        }

        if (!this.delegationSelectorsMap[hashOfNode][selector]) {
          this.delegationSelectorsMap[hashOfNode][selector] = [];
        }
        if (!this.delegationSelectorsMap[hashOfNode][selector][eventType]) {
          this.delegationSelectorsMap[hashOfNode][selector][eventType] = [callback];
        }
      } else {
        let addFunction = true;
        for (let i = 0; i < this.delegationSelectorsMap[hashOfNode][selector][eventType].length; i++) {
          let func = this.delegationSelectorsMap[hashOfNode][selector][eventType][i];
          if (func.toString() === callback.toString()) {
            addFunction = false;
            break;
          }
        }

        if (addFunction) {
          this.delegationSelectorsMap[hashOfNode][selector][eventType].push(callback);
        }
      }
    })

    /**
     * removes the delegation of the element for the provides event types, if no types are provided remove all dellegations
     * @param {?string} type A string containing one or more space-separated JavaScript event types, such as "click" or "keydown," or custom event names.
     */
    return (type) => {
      if (type) {
        _eventTypes = type.split(' ');
      }

      _eventTypes.forEach(eventType => {
        if (!this.delegationSelectorsMap[selector][eventType]) return;

        if (this.delegationSelectorsMap[selector][eventType].length >= 2) {
          this.delegationSelectorsMap[selector][eventType] = this.delegationSelectorsMap[selector][eventType].filter(cb => cb !== callback);
        } else {
          delete this.delegationSelectorsMap[selector][eventType];
        }
      });
    }
  }

  /**
   * @param {Node} node
   * @return {*|string}
   */
  getHashOfNode = (node) => {
    if (node instanceof Document) {
      return 'document';
    }
    return Md5.hash(JSON.stringify(node));
  }
}

export default new Event();
