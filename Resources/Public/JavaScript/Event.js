/**
 * Module: TYPO3/CMS/MindshapeSeo/Event
 *
 * JavaScript to add jQuery.delegate in vanilla js
 * Code copied and modified from https://gist.github.com/iagobruno/4db2ed62dc40fa841bb9a5c7de92f5f8
 * modified to not only support on "click" from now change and click events are supported also like jQuery multiple types can be defined by one call
 * @exports TYPO3/CMS/MindshapeSeo/Event
 */
define([
    'TYPO3/CMS/Backend/Hashing/Md5'
], function (Md5) {
  'use strict';

  let Event = {
    delegationSelectorsMap: {},
    supportedEvents: ['change', 'click', 'keyup'],
    nodesHasEventListener: []
  };

  /**
   *
   * @param {Event} event
   */
  Event.listener = (event) => {
    let elementRegisteredNode = event.currentTarget;
    let elementRegisteredNodeHash = Event.getHashOfNode(elementRegisteredNode)
    let element = event.target;
    let type = event.type;
    let forceBreak = false;

    const searchForMatches = function (event, key) {
      for (const selector in Event.delegationSelectorsMap[key]) {
        if (element.matches(selector)) {
          event.stopPropagation = function () {
            forceBreak = true;
          };

          const callbackList = Event.delegationSelectorsMap[key][selector][type];
          if (callbackList) {
            callbackList.forEach(function (callback) { callback(element, event); });
          }
        }
      }
    }

    while (element && element !== document.documentElement) {
      if (Event.delegationSelectorsMap[elementRegisteredNodeHash]) {
        searchForMatches(event, elementRegisteredNodeHash);
      } else {
        let objectKeys = Object.keys(Event.delegationSelectorsMap);
        objectKeys.forEach(key => {
          searchForMatches(event, key);
        })
      }

      if (forceBreak) break;

      element = element.parentElement;
    }
  }

  /**
   *
   * @param {Node} node
   * @param {?string} type
   */
  Event.addEvent = (node, type) => {
    if (typeof node === 'undefined') throw new Error(`The provided element is empty!`);
    if (type && type !== '' && !Event.supportedEvents.includes(type)) throw new Error(`The provided type "${type}" is currently not supported! Supported EventTypes: ${Event.supportedEvents.join(' ')}`);
    if (!Event.nodesHasEventListener.includes(node) || !Event.nodesHasEventListener[node].includes(type)) {
      if (typeof type !== 'undefined' && type !== '') {
        node.addEventListener(type, Event.listener);
      } else {
        Event.supportedEvents.forEach(eventType => {
          node.addEventListener(eventType, Event.listener)
        });
      }

      if (!Event.nodesHasEventListener.includes(node)) {
        Event.nodesHasEventListener[node] = [];
      }

      Event.nodesHasEventListener[node].push(type);
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
  Event.delegate = (eventTypes, selector, callback, node = document) => {
    if (typeof eventTypes === 'undefined' || eventTypes === '') throw new Error('The provided event is empty.');
    if (typeof selector === 'undefined' || selector === '') throw new Error('The provided selector is empty.');
    if (typeof callback === 'undefined' || typeof callback !== 'function') throw new Error('Specify an callback.');


    let _eventTypes = eventTypes.split(' ');

    _eventTypes.forEach(eventType => {
      if (!Event.supportedEvents.includes(eventType)) throw new Error('Event is currently not supported');
      Event.addEvent(node, eventType);

      let hashOfNode = Event.getHashOfNode(node);

      if (!Event.delegationSelectorsMap[hashOfNode]  || !Event.delegationSelectorsMap[hashOfNode][selector] || !Event.delegationSelectorsMap[hashOfNode][selector][eventType]) {
        if (!Event.delegationSelectorsMap[hashOfNode]) {
          Event.delegationSelectorsMap[hashOfNode] = [];
        }

        if (!Event.delegationSelectorsMap[hashOfNode][selector]) {
          Event.delegationSelectorsMap[hashOfNode][selector] = [];
        }
        if (!Event.delegationSelectorsMap[hashOfNode][selector][eventType]) {
          Event.delegationSelectorsMap[hashOfNode][selector][eventType] = [callback];
        }
      }
      else {
        let addFunction = true;
        for (let i = 0; i < Event.delegationSelectorsMap[hashOfNode][selector][eventType].length; i++) {
          let func = Event.delegationSelectorsMap[hashOfNode][selector][eventType][i];
          if (func.toString() === callback.toString()) {
            addFunction = false;
            break;
          }
        }

        if (addFunction) {
          Event.delegationSelectorsMap[hashOfNode][selector][eventType].push(callback);
        }
      }
    })
    /**
     * removes the delegation of the element for the provides event types, if no types are provided remove all dellegations
     * @param {?string} type A string containing one or more space-separated JavaScript event types, such as "click" or "keydown," or custom event names.
     */
    function unsubscribeFN (type) {
      if (type) {
        _eventTypes = type.split(' ');
      }

      _eventTypes.forEach(eventType => {
        if(!Event.delegationSelectorsMap[selector][eventType]) return;

        if (Event.delegationSelectorsMap[selector][eventType].length >= 2) {
          Event.delegationSelectorsMap[selector][eventType] = Event.delegationSelectorsMap[selector][eventType].filter(cb => cb !== callback);
        }
        else {
          delete Event.delegationSelectorsMap[selector][eventType];
        }
      });
    }


    return unsubscribeFN;
  }

  Event.getHashOfNode = (node) => {
    if (node instanceof Document) {
      return 'document';
    }
    return Md5.hash(JSON.stringify(node));
  }

  return Event;
});
