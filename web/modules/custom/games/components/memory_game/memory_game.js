const cards = [
  "🐶", "🙈", "🍎", "🚀", "🏀", "🎧",
  "🐶", "🙈", "🍎", "🚀", "🏀", "🎧"
];

let MemoryGame = createReactClass({
  getInitialState: function () {
    return {
      cards: this.shuffleCards([...cards]),  // Максимально змішані картки
      openedCards: [],
      matchedCards: [],
      score: 0,
      message: ''
    };
  },

  shuffleCards: function (cards) {
    for (let i = cards.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [cards[i], cards[j]] = [cards[j], cards[i]];
    }
    return cards;
  },

  handleCardClick: function (index) {
    if (this.state.openedCards.length === 2 || this.state.matchedCards.includes(index)) {
      return;
    }

    const openedCards = [...this.state.openedCards, index];
    this.setState({ openedCards });

    if (openedCards.length === 2) {
      const [firstIndex, secondIndex] = openedCards;
      if (this.state.cards[firstIndex] === this.state.cards[secondIndex]) {
        this.setState({
          matchedCards: [...this.state.matchedCards, firstIndex, secondIndex],
          score: this.state.score + 1,
          message: 'Ви знайшли пару!',
          openedCards: []
        });
      } else {
        setTimeout(() => {
          this.setState({ openedCards: [], message: 'Спробуйте знову!' });
        }, 1000);
      }
    }
  },

  render: function () {
    const { cards, openedCards, matchedCards, score, message } = this.state;

    return (
      <div className="memory-game-container">
        <h1>Гра "Пам'ять"</h1>
        <p>Ваш рахунок: {score}</p>
        {message && <p className="message">{message}</p>}
        <div className="memory-board">
          {cards.map((card, index) => (
            <div
              key={index}
              className={`memory-card ${openedCards.includes(index) || matchedCards.includes(index) ? 'flipped' : ''}`}
              onClick={() => this.handleCardClick(index)}
            >
              <div className="memory-card-inner">
                <div className="memory-card-front">
                  ❓  {/* задня сторона картки */}
                </div>
                <div className="memory-card-back">
                  {card}  {/* передня сторона картки */}
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    );
  }
});

// Отримуємо ID елементу, де буде рендеритись компонент
const element = document.getElementById('react_memory_game');

// Рендеримо компонент
if (element) {
  ReactDOM.render(<MemoryGame />, element);
}
