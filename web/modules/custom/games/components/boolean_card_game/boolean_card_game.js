let BooleanCardGame = createReactClass({
  getInitialState: function () {
    return {
      cards: [
        { statement: 'Інформатика - це наука про різні типи інформації', answer: false },
        { statement: 'Комп\'ютер - це пристрій для виконання обчислень та обробки даних', answer: true },
        { statement: 'Інтернет - це те саме, що Інтранет', answer: false },
        { statement: 'Програмування - це процес створення програм для роботи на комп\'ютері', answer: true },
        { statement: 'Мова програмування "Python" отримала свою назву через відомого змієборця', answer: true },
        { statement: 'Мова програмування "HTML" використовується для створення статичних веб-сайтів', answer: false },
        { statement: 'База даних - це місце для зберігання даних у відсортованому вигляді', answer: true },
        { statement: 'Система числення "двійкова" використовує дві цифри: 0 і 1', answer: true },
        { statement: 'Сайт - це інтерактивна графіка, створена для розваг', answer: false },
        { statement: 'URL - це абревіатура від "Uniform Resource Locator"', answer: true },
      ],
      score: 0
    };
  },

  handleAnswer: function (answer) {
    const nextCard = this.state.cards[0];
    if (nextCard.answer === answer) {
      this.setState({ score: this.state.score + 1 });
    }
    this.setState({ cards: this.state.cards.slice(1) });
  },

  render: function () {
    const { cards, score } = this.state;

    return (
      <div className="boolean-card-game-container">
        <p>Ваш рахунок: {score}</p>
        {cards.length > 0 ? (
          <div className="card">
            <p>{cards[0].statement}</p>
            <button onClick={() => this.handleAnswer(true)}>Правда</button>
            <button onClick={() => this.handleAnswer(false)}>Брехня</button>
          </div>
        ) : (
          <p>Гра закінчена. Ваш загальний рахунок: {score}</p>
        )}
      </div>
    );
  }
});

// Отримуємо ID елементу, де буде рендеритись компонент
const element = document.getElementById('react_boolean_card_game');

// Рендеримо компонент
if (element) {
  ReactDOM.render(<BooleanCardGame />, element);
}
