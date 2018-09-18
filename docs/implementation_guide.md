# CyberWars API Implementation Guide

## Overview
The game can be broken into three sections of interactions:
1. Lobby
2. Game
3. Turns

## The OpenAPISpec 3 file
Throught this guide you will see references to operations and models in the API. This document will avoid going too deep into the definitions and examples of those operations and models. They are already well defined in the following document, which we will convert to Mark Down some day:
[OAS3 File](api_spec_oas3.yml)

## Polling Vs. Pusher
The server will be firing Pusher events on a channel named ```game-{gameId}```. If you are a client that can listen to events on that channel, you should. Otherwise the API will always have an endpoint you can poll to get the same information, albiet less efficently.
Event names will be described as they are needed, and the endpoint to poll will be described as well.

## Lobby
The lobby secion is where you will join a game and gain all meta information needed before you can start turns.
Your first goal is to get a GameID. This is a secret ID that gives you access to meta information about a game, and lets you join as a player if there is a slot available.
### Step 1A: Creating a game from scratch
If you are starting a new game, you get a gameID by sending the ```createGame``` operation to the API. This will create a game, and give you that game back, which includes the gameID you will be using. Keep in mind that the returned game has all of the map tile information ready to go at this point.
### Step 1B: Joining an existing game
If you already have an existing gameID, by getting one from another player that they want you to join for example, you can make a ```getGame``` request to get the game from that ID. Keep in mind that the returned game has all of the map tile information ready to go at this point.
### Step 2: Create a player for yourself
Once you have the game object, you can create a player for yourself. Send the ```addPlayer``` operation to get a player (including a secret PlayerID) to play as.
**Note:** Keep in mind that a player has both a public "number" (from 1 - max players), as well as a secret id that will be used in furture requests.

## Game
TODO: Summary

### Knowing when it's your turn
TODO:
- poll game for playernumber to match yours
- listen for turn-start event on pusher channel

### Unit Info
TODO:
## Turns
TODO:

### Starting a turn
TODO:
### Actions on a turn
TODO:
### Ending a turn
TODO:
