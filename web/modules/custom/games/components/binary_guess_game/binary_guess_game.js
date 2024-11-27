let BinaryGuessGame = createReactClass({
  getInitialState: function () {
    return {
      questions: this.generateQuestions(10), // Генеруємо 10 питань
      currentQuestionIndex: 0,
      guess: '',
      score: 0,
      message: ''
    };
  },

  generateQuestions: function (numQuestions) {
    const questions = [];
    for (let i = 0; i < numQuestions; i++) {
      const randomNumber = Math.floor(Math.random() * 16); // Генеруємо числа від 0 до 15 (четвертий розряд)
      questions.push({
        binary: randomNumber.toString(2).padStart(4, '0'), // Конвертуємо до бінарного формату
        decimal: randomNumber
      });
    }
    return questions;
  },

  handleInputChange: function (e) {
    this.setState({ guess: e.target.value });
  },

  handleSubmit: function (e) {
    e.preventDefault();
    const { questions, currentQuestionIndex, guess, score } = this.state;
    const currentQuestion = questions[currentQuestionIndex];

    if (parseInt(guess) === currentQuestion.decimal) {
      this.setState({
        score: score + 1,
        message: 'Правильно!',
        currentQuestionIndex: currentQuestionIndex + 1,
        guess: ''
      });
    } else {
      this.setState({
        message: `Неправильно! Відповідь: ${currentQuestion.decimal}`,
        currentQuestionIndex: currentQuestionIndex + 1,
        guess: ''
      });
    }
  },

  render: function () {
    const { questions, currentQuestionIndex, guess, score, message } = this.state;
    const currentQuestion = questions[currentQuestionIndex];

    return (
      <div className="binary-guess-game-container">
        <h1>Вгадати бінарне число</h1>
        <p>Ваш рахунок: {score}</p>
        {currentQuestionIndex < questions.length ? (
          <div>
            <div className="question">
              <p>Чому дорівнює {currentQuestion.binary} в десятковій системі?</p>
            </div>
            <form onSubmit={this.handleSubmit}>
              <input
                type="number"
                value={guess}
                onChange={this.handleInputChange}
                placeholder="Ваша відповідь"
                required
              />
              <button type="submit">Відправити</button>
            </form>
            {message && <p className="message">{message}</p>}
          </div>
        ) : (
          <div>
            <p>Гра закінчена. Ваш загальний рахунок: {score}</p>
          </div>
        )}
      </div>
    );
  }
});

// Отримуємо ID елементу, де буде рендеритись компонент
const element = document.getElementById('react_binary_guess_game');

// Перевірка наявності елементу
console.log(element);

// Рендеримо компонент
if (element) {
  ReactDOM.render(<BinaryGuessGame />, element);
}
