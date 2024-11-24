# Live Quiz System Documentation

## Overview
The live quiz system allows teachers to host real-time quiz sessions where students join using a generated access code. This creates an interactive, Kahoot-like experience with live leaderboards and instant feedback.

## Core Features

### 1. Quiz Code Generation
Located in `quiz.js` (lines 255-264), the system generates unique 6-character alphanumeric codes for quiz access.

### 2. Live Quiz Modes
- **Synchronous Mode**: All students answer questions simultaneously
- **Time-based Scoring**: Points awarded based on speed and accuracy
- **Real-time Leaderboard**: Live updates of student rankings
- **Interactive Feedback**: Immediate response validation

### 3. Teacher Controls
- Start/pause quiz sessions
- Monitor student progress
- Control question timing
- View live participation stats

### 4. Student Experience
- Join via access code
- Real-time question display
- Instant feedback
- Live rank updates
- Score animations

### 5. Scoring System
Points are calculated based on:
- Correct answer: 1000 base points
- Time bonus: Up to 500 points
- Answer streak: Multiplier bonus

### 6. Gamification Elements
- Answer streaks
- Time bonus points
- Special achievements
- Sound effects
- Victory animations

## Technical Implementation

### WebSocket Integration
The live quiz system uses WebSocket connections for real-time updates: