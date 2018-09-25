# CyberWars API Implementation Guide

## Overview
The game can be broken into three sections of interactions:
1. Lobby
2. Game
3. Turns

### The OpenAPISpec 3 file
Throughout this guide you will see references to operations and models in the API. This document will avoid going too deep into the definitions and examples of those operations and models. They are already well defined in the following document, which we will convert to Mark Down some day:
[OAS3 File](api_spec_oas3.yml)

Swagger editor link: http://editor.swagger.io/#/?import=https://raw.githubusercontent.com/johnvandeweghe/CyberBack/master/docs/api_spec_oas3.yml

### Polling Vs. Pusher
The server will be firing Pusher events on a channel named ```game-{gameId}```. If you are a client that can listen to events on that channel, you should. Otherwise the API will always have an endpoint you can poll to get the same information, albiet less efficently.
Event names will be described as they are needed, and the endpoint to poll will be described as well.

## Lobby
The lobby section is where you will join a game and gain all meta information needed before you can start turns.
Your first goal is to get a GameID. This is a secret ID that gives you access to meta information about a game, and lets you join as a player if there is a slot available.
### Step 1A: Creating a game from scratch
If you are starting a new game, you get a gameID by sending the ```createGame``` operation to the API. This will create a game, and give you that game back, which includes the gameID you will be using. Keep in mind that the returned game has all of the map tile information ready to go at this point.
### Step 1B: Joining an existing game
If you already have an existing gameID, by getting one from another player that they want you to join for example, you can make a ```getGame``` request to get the game from that ID. Keep in mind that the returned game has all of the map tile information ready to go at this point.
### Step 2: Create a player for yourself
Once you have the game object, you can create a player for yourself. Send the ```addPlayer``` operation to get a player (including a secret PlayerID) to play as.
**Note:** Keep in mind that a player has both a public "number" (from 1 - max players), as well as a secret id that will be used in furture requests.

## Game
This section is for actions that you may take during a game (even if it is not your turn).

### Unit Info
You can either get all of the units with ```getUnits``` or a specific unit with ```getUnit```.
This can be done at any time, and should be done often when polling.

## Turns
This section describes how to do turns.

### Knowing when it's your turn
You have two options for this:
- **Polling:** Poll game using ```getGame``` and check the playerNumber, if it matches yours you can now make a turn.
- **Pusher:** Listen for ```turn-start``` event, it will have a playerNumber field on it, if it matches yours you can make a turn.

### Starting a turn
**Note:** Start every turn by getting all units to ensure you have an up to date game state.
Then call ```startTurn``` to get a turnID to use in subsequent requests.

### Actions on a turn
Each unit is allowed a certain amount of Action Points. Each action will use a number of points based on the action. A unit recovers a defined amount of points at the beginning of a player's turn.
You may end your turn before using all of your action points, but units have a maximum amount of points that can hold.

#### Making a movement action
To make a movement with a unit, send a ```createUnitAction``` with a type of "move" and an args of type ```MoveActionArgs```.
Within that args object you will need to send a path your unit wants to take. This path must consist of single square adjacent only movements. No diagonals. Each unit may move up to their speed with a single action point. The total number of squares moved must not exceed that unit's alloted action points.

#### Attacking a unit
To attack a unit, send a ```createUnitAction``` with a type of "attack", and the unitID you are targeting. The target unit must be within the attacking unit's attack range, following the same rules as movement to calculate distance. Attack actions use your unit's attack stat number of action points.
To see the effects of your action, do a ```getUnit``` on each unit that is in the response's affectedUnitIds list.

### Ending a turn
To end your turn, send an ```updateTurn``` request, setting the status of your turn to "turn-complete".
**Note:** As stated above, after each turn you will receive a pusher event to tell you who is next.
