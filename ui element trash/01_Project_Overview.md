# BhraminSathi — Project Overview

## Problem
In most Tier-2/3 Indian cities, public transport (local buses) exists but is hard to use for anyone who isn't already familiar with the network. A traveller may know their destination but not which bus to take, where to board, where to change, or whether the bus they're looking at is even going the right way. There's no official live tracking infrastructure in most of these cities.

## Core Idea
BhraminSathi is a public-transport journey assistant built around one rule: **the user should never need to already understand the city's bus network.**

The user enters a destination. The app shows nearby live buses and helps them identify the right one — with a human-verified handoff (the conductor) as a safety net on top of GPS, since GPS alone can be wrong or imprecise.

## Why "Conductor GPS" Instead of Passenger Crowdsourcing
Most transport apps rely on either official transit APIs (rare in Tier-2/3 cities) or opted-in passenger phones (unreliable — depends on whether any app user happens to be riding). BhraminSathi instead uses the **conductor's phone** as the location source:

- In India, a bus legally/practically cannot run without a conductor — so a conductor is present on every active bus, guaranteed.
- One bus = one active conductor session = one reliable live location feed.
- This turns an unreliable crowdsourcing problem into a much simpler "track one known device per bus" problem.

## Differentiation
BhraminSathi is not "another live bus tracker." Its actual differentiation is the **combination**:
- No official transit infrastructure required (conductor-sourced GPS instead)
- First-timer-friendly journey guidance (not just a map — clear source/destination flow)
- A built-in human verification layer (passenger confirms the destination with the conductor when boarding, since GPS shows "likely," not "certain")
- Honest data — the app never shows a live position it doesn't actually have; buses without a connected conductor show only static route info

## Team
Bhavesh, Ashish, Mudit, Shital, Komal, Bhumika — SIH (Smart India Hackathon) team project.

## Prototype Goal
Prove the full loop works end-to-end: passenger searches → sees live buses (conductor-sourced) → identifies the right one → conductor dashboard demonstrates the location source → admin panel shows the system operating and handling failures gracefully.
